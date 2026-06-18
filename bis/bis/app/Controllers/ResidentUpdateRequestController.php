<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ResidentUpdateRequestModel;

class ResidentUpdateRequestController extends BaseController
{
    private ResidentUpdateRequestModel $requestModel;

    public function __construct()
    {
        $this->requestModel = new ResidentUpdateRequestModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status') ?? 'pending';

        $allowedStatuses = [
            'pending',
            'under_review',
            'approved',
            'rejected',
            'cancelled',
            'all',
        ];

        if (! in_array($status, $allowedStatuses, true)) {
            $status = 'pending';
        }

        $builder = $this->requestModel->orderBy('created_at', 'DESC');

        if ($status !== 'all') {
            $builder->where('status', $status);
        }

        $requests = $builder->findAll();

        return view('dashboard/resident_update_requests/index', [
            'requests' => $requests,
            'status' => $status,
        ]);
    }

    public function show($id)
    {
        $request = $this->requestModel->find($id);

        if (! $request) {
            return redirect()
                ->back()
                ->with('error', 'Resident update request not found.');
        }

        return view('dashboard/resident_update_requests/show', [
            'request' => $request,
        ]);
    }

    public function approve($id)
    {
        $request = $this->requestModel->find($id);

        if (! $request) {
            return redirect()
                ->back()
                ->with('error', 'Resident update request not found.');
        }

        if (! in_array($request['status'], ['pending', 'under_review'], true)) {
            return redirect()
                ->back()
                ->with('error', 'Only pending or under review requests can be approved.');
        }

        $reviewNotes = trim((string) $this->request->getPost('review_notes'));

        $this->requestModel->update($id, [
            'status' => 'approved',
            'reviewed_by' => $this->getCurrentStaffId(),
            'review_notes' => $reviewNotes !== '' ? $reviewNotes : 'Approved by barangay staff.',
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()
            ->to($this->getRolePrefix() . '/resident-update-requests/' . $id)
            ->with('success', 'Resident update request approved. Official records must still be updated manually by authorized staff.');
    }

    public function reject($id)
    {
        $request = $this->requestModel->find($id);

        if (! $request) {
            return redirect()
                ->back()
                ->with('error', 'Resident update request not found.');
        }

        if (! in_array($request['status'], ['pending', 'under_review'], true)) {
            return redirect()
                ->back()
                ->with('error', 'Only pending or under review requests can be rejected.');
        }

        $reviewNotes = trim((string) $this->request->getPost('review_notes'));

        if ($reviewNotes === '') {
            return redirect()
                ->back()
                ->with('error', 'Review notes are required when rejecting a request.');
        }

        $this->requestModel->update($id, [
            'status' => 'rejected',
            'reviewed_by' => $this->getCurrentStaffId(),
            'review_notes' => $reviewNotes,
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()
            ->to($this->getRolePrefix() . '/resident-update-requests/' . $id)
            ->with('success', 'Resident update request rejected.');
    }

    public function markUnderReview($id)
    {
        $request = $this->requestModel->find($id);

        if (! $request) {
            return redirect()
                ->back()
                ->with('error', 'Resident update request not found.');
        }

        if ($request['status'] !== 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Only pending requests can be marked as under review.');
        }

        $this->requestModel->update($id, [
            'status' => 'under_review',
        ]);

        return redirect()
            ->to($this->getRolePrefix() . '/resident-update-requests/' . $id)
            ->with('success', 'Resident update request marked as under review.');
    }

    private function getCurrentStaffId(): ?int
    {
        $session = session();

        $possibleKeys = [
            'user_id',
            'admin_id',
            'captain_id',
            'secretary_id',
            'id',
        ];

        foreach ($possibleKeys as $key) {
            $value = $session->get($key);

            if (is_numeric($value)) {
                return (int) $value;
            }
        }

        return null;
    }

    private function getRolePrefix(): string
    {
        $role = service('uri')->getSegment(1);

        if (in_array($role, ['captain', 'secretary'], true)) {
            return '/' . $role;
        }

        return '/secretary';
    }
}