<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BlotterModel;
use App\Models\UserModel;
use App\Libraries\EmailService;

class BlotterController extends BaseController
{
    protected BlotterModel $model;

    public function __construct()
    {
        $this->model = new BlotterModel();
    }

    // ── Public (non-resident): submit blotter report ─────────────────────────

    public function storePublic()
    {
        $complainantName    = trim($this->request->getPost('complainant_name') ?? '');
        $complainantEmail   = trim($this->request->getPost('complainant_email') ?? '');
        $complainantContact = trim($this->request->getPost('complainant_contact') ?? '');
        $complainantAddress = trim($this->request->getPost('complainant_address') ?? '');
        $incidentType       = $this->request->getPost('incident_type');
        $incidentDate       = $this->request->getPost('incident_date');
        $incidentTime       = $this->request->getPost('incident_time');
        $location           = $this->request->getPost('location');
        $personsInvolved    = $this->request->getPost('persons_involved');
        $narrative          = trim($this->request->getPost('narrative') ?? '');

        if (empty($complainantName) || empty($complainantEmail) || empty($incidentType) || empty($narrative)) {
            return redirect()->back()->with('error', 'Please fill in all required fields.')->withInput();
        }

        // Use a placeholder user_id of 0 for non-residents (or create a guest user concept)
        $this->model->insert([
            'complainant_user_id' => null, // null = non-resident / walk-in
            'complainant_name'    => $complainantName,
            'complainant_email'   => $complainantEmail,
            'incident_type'       => $incidentType,
            'incident_date'       => $incidentDate ?: null,
            'incident_time'       => $incidentTime ?: null,
            'location'            => $location ?: null,
            'persons_involved'    => $personsInvolved ?: null,
            'narrative'           => $narrative,
            'respondent_address'  => $complainantAddress ?: null, // store as context
            'status'              => 'pending',
        ]);

        return redirect()->to('/')->with('blotter_success', 'Your blotter report has been submitted successfully. The barangay will contact you at ' . $complainantEmail . ' once a hearing is scheduled.');
    }

    // ── Resident: submit blotter report ──────────────────────────────────────

    public function store()
    {
        $userId    = (int) session()->get('user_id');
        $userModel = new UserModel();
        $user      = $userModel->find($userId);

        $incidentType    = $this->request->getPost('incident_type');
        $incidentDate    = $this->request->getPost('incident_date');
        $incidentTime    = $this->request->getPost('incident_time');
        $location        = $this->request->getPost('location');
        $personsInvolved = $this->request->getPost('persons_involved');
        $narrative       = trim($this->request->getPost('narrative') ?? '');

        if (empty($incidentType) || empty($narrative)) {
            return redirect()->back()->with('blotter_error', 'Please fill in the required fields.')->withInput();
        }

        $this->model->insert([
            'complainant_user_id' => $userId,
            'complainant_name'    => $user['full_name'] ?? '',
            'complainant_email'   => $user['email'] ?? '',
            'incident_type'       => $incidentType,
            'incident_date'       => $incidentDate ?: null,
            'incident_time'       => $incidentTime ?: null,
            'location'            => $location ?: null,
            'persons_involved'    => $personsInvolved ?: null,
            'narrative'           => $narrative,
            'status'              => 'pending',
        ]);

        return redirect()->to('/resident/dashboard')->with('success', 'Blotter report submitted successfully. The barangay will contact you shortly.');
    }

    // ── Admin: list all blotter reports ──────────────────────────────────────

    public function adminIndex(string $role)
    {
        $statusFilter = $_GET['status'] ?? '';
        $search       = $_GET['search'] ?? '';

        $db      = \Config\Database::connect();
        $builder = $db->table('blotter_reports b')
            ->select('b.*, u.full_name AS complainant_full_name, u.email AS complainant_email_addr')
            ->join('users u', 'u.id = b.complainant_user_id', 'left')
            ->orderBy('b.created_at', 'DESC');

        if ($statusFilter !== '') {
            $builder->where('b.status', $statusFilter);
        }
        if ($search !== '') {
            $builder->groupStart()
                ->like('u.full_name', $search)
                ->orLike('b.incident_type', $search)
                ->orLike('b.persons_involved', $search)
                ->groupEnd();
        }

        $reports = $builder->get()->getResultArray();

        $pending       = $this->model->where('status', 'pending')->countAllResults();
        $investigating = $this->model->where('status', 'under_investigation')->countAllResults();
        $resolved      = $this->model->where('status', 'resolved')->countAllResults();
        $total         = $this->model->countAll();

        $viewFile = ($role === 'captain')
            ? 'dashboard/captain/blotter'
            : 'dashboard/secretary/blotter';

        return view($viewFile, [
            'reports'       => $reports,
            'pending'       => $pending,
            'investigating' => $investigating,
            'resolved'      => $resolved,
            'total'         => $total,
            'statusFilter'  => $statusFilter,
            'search'        => $search,
        ]);
    }

    // ── Admin: view single blotter report ────────────────────────────────────

    public function show(int $id)
    {
        $role   = (string)(session()->get('role') ?? 'captain');
        $db     = \Config\Database::connect();
        $report = $db->table('blotter_reports b')
            ->select('b.*, u.full_name AS complainant_full_name, u.email AS complainant_email_addr')
            ->join('users u', 'u.id = b.complainant_user_id', 'left')
            ->where('b.id', $id)
            ->get()->getRowArray();

        if (! $report) {
            return redirect()->to('/' . $role . '/blotter')->with('error', 'Report not found.');
        }

        return view('dashboard/captain/blotter_detail', [
            'report' => $report,
            'role'   => $role,
        ]);
    }

    // ── Admin: update status ──────────────────────────────────────────────────

    public function updateStatus(int $id)
    {
        $role    = (string)(session()->get('role') ?? 'captain');
        $status  = $this->request->getPost('status');
        $remarks = $this->request->getPost('remarks') ?? '';

        $this->model->update($id, [
            'status'       => $status,
            'remarks'      => $remarks,
            'processed_by' => session()->get('user_id'),
        ]);

        return redirect()->to('/' . $role . '/blotter/' . $id)->with('success', 'Status updated.');
    }

    public function sendSummons(int $id)
    {
        $role   = (string)(session()->get('role') ?? 'captain');
        $report = $this->model->find($id);

        if (! $report) {
            return redirect()->back()->with('error', 'Report not found.');
        }

        $hearingDate = $this->request->getPost('hearing_date');
        $hearingTime = $this->request->getPost('hearing_time');
        $respondentName  = trim($this->request->getPost('respondent_name') ?? '');
        $respondentEmail = trim($this->request->getPost('respondent_email') ?? '');
        $respondentAddr  = trim($this->request->getPost('respondent_address') ?? '');

        if (empty($hearingDate) || empty($hearingTime)) {
            return redirect()->back()->with('error', 'Please set a hearing date and time.');
        }

        // Save respondent info + hearing schedule
        $this->model->update($id, [
            'respondent_name'    => $respondentName,
            'respondent_email'   => $respondentEmail,
            'respondent_address' => $respondentAddr,
            'hearing_date'       => $hearingDate,
            'hearing_time'       => $hearingTime,
            'status'             => 'under_investigation',
            'summons_sent_at'    => date('Y-m-d H:i:s'),
            'processed_by'       => session()->get('user_id'),
        ]);

        $caseNo      = str_pad($id, 4, '0', STR_PAD_LEFT);
        $incidentType = $report['incident_type'];
        $hDate       = date('F d, Y', strtotime($hearingDate));
        $hTime       = date('h:i A', strtotime($hearingTime));

        $emailService = new EmailService();
        $errors       = [];

        // Send to complainant
        try {
            $emailService->sendSummons(
                $report['complainant_email'],
                $report['complainant_name'],
                $caseNo,
                $incidentType,
                $hDate,
                $hTime,
                'complainant'
            );
        } catch (\Throwable $e) {
            $errors[] = 'Could not send to complainant: ' . $e->getMessage();
            log_message('error', 'Summons to complainant failed: ' . $e->getMessage());
        }

        // Send to respondent (if email provided)
        if (! empty($respondentEmail)) {
            try {
                $emailService->sendSummons(
                    $respondentEmail,
                    $respondentName ?: 'Respondent',
                    $caseNo,
                    $incidentType,
                    $hDate,
                    $hTime,
                    'respondent'
                );
            } catch (\Throwable $e) {
                $errors[] = 'Could not send to respondent: ' . $e->getMessage();
                log_message('error', 'Summons to respondent failed: ' . $e->getMessage());
            }
        }

        if (! empty($errors)) {
            return redirect()->to('/' . $role . '/blotter/' . $id)
                ->with('error', implode(' | ', $errors));
        }

        return redirect()->to('/' . $role . '/blotter/' . $id)
            ->with('success', 'Hearing Schedule saved successfully.');
    }

    // ── Admin: reschedule hearing ─────────────────────────────────────────────

    public function reschedule(int $id)
    {
        $role        = (string)(session()->get('role') ?? 'captain');
        $hearingDate = $this->request->getPost('hearing_date');
        $hearingTime = $this->request->getPost('hearing_time');
        $notes       = trim($this->request->getPost('hearing_notes') ?? '');

        if (empty($hearingDate) || empty($hearingTime)) {
            return redirect()->back()->with('error', 'Please provide both a date and time for the hearing.');
        }

        $this->model->update($id, [
            'hearing_date'  => $hearingDate,
            'hearing_time'  => $hearingTime,
            'hearing_notes' => $notes ?: null,
            'scheduled_by'  => session()->get('user_id'),
            'status'        => 'under_investigation',
        ]);

        return redirect()->to('/' . $role . '/blotter/' . $id)
            ->with('success', 'Hearing schedule updated successfully.');
    }

    // ── Admin: view/print summons letter ─────────────────────────────────────

    public function viewLetter(int $id)
    {
        $role   = (string)(session()->get('role') ?? 'captain');
        $db     = \Config\Database::connect();
        $report = $db->table('blotter_reports b')
            ->select('b.*, u.full_name AS complainant_full_name, u.email AS complainant_email_addr')
            ->join('users u', 'u.id = b.complainant_user_id', 'left')
            ->where('b.id', $id)
            ->get()->getRowArray();

        if (! $report) {
            return redirect()->to('/' . $role . '/blotter')->with('error', 'Report not found.');
        }

        // Mark letter as issued
        $this->model->update($id, ['letter_issued_at' => date('Y-m-d H:i:s')]);

        return view('blotter_letter', [
            'report' => $report,
            'role'   => $role,
        ]);
    }
}
