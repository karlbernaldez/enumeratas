<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class UIController extends BaseController
{
    // ── Shared census view builder (captain + secretary) ──────────────────────
    private function _censusView(string $role): \CodeIgniter\HTTP\ResponseInterface|string
    {
        $db = \Config\Database::connect();

        // ── Active filters from GET ───────────────────────────────────────
        $filters = [
            'zone'        => trim($_GET['zone']        ?? ''),
            'gender'      => trim($_GET['gender']      ?? ''),
            'age_min'     => trim($_GET['age_min']     ?? ''),
            'age_max'     => trim($_GET['age_max']     ?? ''),
            'is_pwd'      => trim($_GET['is_pwd']      ?? ''),
            'is_senior'   => trim($_GET['is_senior']   ?? ''),
            'is_solo'     => trim($_GET['is_solo']     ?? ''),
            'is_4ps'      => trim($_GET['is_4ps']      ?? ''),
            'is_student'  => trim($_GET['is_student']  ?? ''),
            'is_indigent' => trim($_GET['is_indigent'] ?? ''),
            'search'      => trim($_GET['search']      ?? ''),
        ];

        // ── Determine filter mode ─────────────────────────────────────────
        // Any filter that can match members (gender, age, student) needs the
        // UNION view. Head-only flags (pwd, solo, 4ps, senior, indigent) stay
        // on the households table only.
        $needsUnion = (
            $filters['gender']     !== '' ||
            $filters['age_min']    !== '' ||
            $filters['age_max']    !== '' ||
            $filters['is_student'] !== ''
        );

        $hasAnyFilter = (
            $filters['zone']       !== '' ||
            $filters['gender']     !== '' ||
            $filters['age_min']    !== '' ||
            $filters['age_max']    !== '' ||
            $filters['is_pwd']     !== '' ||
            $filters['is_senior']  !== '' ||
            $filters['is_solo']    !== '' ||
            $filters['is_4ps']     !== '' ||
            $filters['is_student'] !== '' ||
            $filters['is_indigent'] !== '' ||
            $filters['search']     !== ''
        );

        // hasSpecialFilter drives the view to show the filter-results table
        $hasSpecialFilter = $hasAnyFilter;

        // ── Stats: respect zone filter, always count heads only ───────────
        $hm = new \App\Models\HouseholdModel();
        $statBase = clone $hm;
        if ($filters['zone'] !== '') {
            $statBase->where('zone', $filters['zone']);
        }

        $stats = [
            'totalHouseholds' => (clone $statBase)->countAllResults(),
            'totalMale'       => (clone $statBase)->where('gender', 'Male')->countAllResults(),
            'totalFemale'     => (clone $statBase)->where('gender', 'Female')->countAllResults(),
            'pwds'            => (clone $statBase)->where('is_pwd', 1)->countAllResults(),
            'fourPs'          => (clone $statBase)->where('is_4ps', 1)->countAllResults(),
            'seniors'         => (clone $statBase)->where('is_senior_citizen', 1)->countAllResults(),
            'soloParent'      => (clone $statBase)->where('is_solo_parent', 1)->countAllResults(),
        ];

        $perPage = 15;
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $offset  = ($page - 1) * $perPage;

        // ── Build WHERE clauses for households (heads) ────────────────────
        $hw = [];   // head WHERE conditions
        $mw = [];   // member WHERE conditions (only used in UNION mode)

        if ($filters['zone'] !== '') {
            $z    = $db->escapeString($filters['zone']);
            $hw[] = "h.zone = '{$z}'";
            $mw[] = "h2.zone = '{$z}'";
        }
        if ($filters['search'] !== '') {
            $s    = $db->escapeLikeString($filters['search']);
            $hw[] = "(h.last_name LIKE '%{$s}%' OR h.first_name LIKE '%{$s}%' OR h.household_no LIKE '%{$s}%')";
            $mw[] = "(m.last_name LIKE '%{$s}%' OR m.first_name LIKE '%{$s}%' OR m.household_no LIKE '%{$s}%')";
        }
        if ($filters['gender'] !== '') {
            $g    = $db->escapeString($filters['gender']);
            $hw[] = "h.gender = '{$g}'";
            $mw[] = "m.gender = '{$g}'";
        }
        if ($filters['is_pwd'] === '1') {
            $hw[] = "h.is_pwd = 1";
            $mw[] = "1=0";
        }
        if ($filters['is_senior'] === '1') {
            $hw[] = "h.is_senior_citizen = 1";
            $mw[] = "1=0";
        }
        if ($filters['is_solo'] === '1') {
            $hw[] = "h.is_solo_parent = 1";
            $mw[] = "1=0";
        }
        if ($filters['is_4ps'] === '1') {
            $hw[] = "h.is_4ps = 1";
            $mw[] = "1=0";
        }
        if ($filters['is_indigent'] === '1') {
            $hw[] = "h.monthly_income > 0 AND h.monthly_income <= 5000";
            $mw[] = "m.monthly_income > 0 AND m.monthly_income <= 5000";
        }
        if ($filters['age_min'] !== '') {
            // age >= age_min  →  born on or before (today - age_min years)
            $d    = date('Y-m-d', strtotime('-' . (int)$filters['age_min'] . ' years'));
            $hw[] = "h.date_of_birth IS NOT NULL AND h.date_of_birth <= '{$d}'";
            $mw[] = "m.date_of_birth IS NOT NULL AND m.date_of_birth <= '{$d}'";
        }
        if ($filters['age_max'] !== '') {
            // age <= age_max  →  born on or after (today - age_max years)
            $d    = date('Y-m-d', strtotime('-' . (int)$filters['age_max'] . ' years'));
            $hw[] = "h.date_of_birth IS NOT NULL AND h.date_of_birth >= '{$d}'";
            $mw[] = "m.date_of_birth IS NOT NULL AND m.date_of_birth >= '{$d}'";
        }
        if ($filters['is_student'] === '1') {
            $hw[] = "UPPER(h.occupation) LIKE '%STUDENT%'";
            $mw[] = "UPPER(m.occupation) LIKE '%STUDENT%'";
        }

        $hwSql = ! empty($hw) ? ' WHERE ' . implode(' AND ', $hw) : '';

        // ── No filter at all: show default heads-only table ───────────────
        if (! $hasAnyFilter) {
            $households = $db->query(
                "SELECT * FROM households h ORDER BY household_no ASC LIMIT {$perPage} OFFSET {$offset}"
            )->getResultArray();
            $totalHouseholdsFiltered = (int) $db->query("SELECT COUNT(*) AS c FROM households")->getRow()->c;

            $viewFile = ($role === 'captain') ? 'dashboard/captain/census' : 'dashboard/secretary/census';
            return view($viewFile, array_merge($stats, [
                'households'             => $households,
                'persons'                => [],
                'hasSpecialFilter'       => false,
                'filteredTotal'          => $totalHouseholdsFiltered,
                'totalHouseholdsFiltered' => $totalHouseholdsFiltered,
                'perPage'                => $perPage,
                'currentPage'            => $page,
                'filters'                => $filters,
            ]));
        }

        // ── Filters active ────────────────────────────────────────────────
        if (! $needsUnion) {
            // ── HEAD-ONLY filter (no age/student) ─────────────────────────
            // All active filters apply only to the households table.
            $sql      = "SELECT * FROM households h{$hwSql} ORDER BY household_no ASC";
            $countSql = "SELECT COUNT(*) AS c FROM households h{$hwSql}";

            $filteredTotal = (int) $db->query($countSql)->getRow()->c;
            $persons       = $db->query("{$sql} LIMIT {$perPage} OFFSET {$offset}")->getResultArray();

            // Add a synthetic 'relationship' column so the view template works
            foreach ($persons as &$p) {
                $p['relationship'] = 'Household Head';
            }
            unset($p);
        } else {
            // ── UNION filter (age or student — includes members) ──────────
            $mwSql = ! empty($mw) ? ' WHERE ' . implode(' AND ', $mw) : '';

            $headSql = "SELECT h.household_no, h.last_name, h.first_name, h.middle_name,
                h.suffix, h.date_of_birth, h.gender, h.civil_status, h.occupation,
                h.monthly_income, h.philhealth_no, h.educational_attainment,
                h.contact_number, h.zone, h.is_pwd, h.is_senior_citizen,
                h.is_solo_parent, h.is_4ps, 'Household Head' AS relationship
                FROM households h{$hwSql}";

            $memberSql = "SELECT m.household_no, m.last_name, m.first_name, m.middle_name,
                m.suffix, m.date_of_birth, m.gender, '' AS civil_status, m.occupation,
                m.monthly_income, m.philhealth_no, m.educational_attainment,
                '' AS contact_number, h2.zone, 0 AS is_pwd, 0 AS is_senior_citizen,
                0 AS is_solo_parent, 0 AS is_4ps, m.relationship
                FROM household_members m
                INNER JOIN households h2 ON h2.household_no = m.household_no{$mwSql}";

            $union         = "({$headSql}) UNION ALL ({$memberSql}) ORDER BY household_no ASC, relationship ASC";
            $filteredTotal = (int) $db->query("SELECT COUNT(*) AS total FROM ({$union}) AS c")->getRow()->total;
            $persons       = $db->query("{$union} LIMIT {$perPage} OFFSET {$offset}")->getResultArray();
        }

        $viewFile = ($role === 'captain') ? 'dashboard/captain/census' : 'dashboard/secretary/census';

        return view($viewFile, array_merge($stats, [
            'households'             => [],
            'persons'                => $persons,
            'hasSpecialFilter'       => true,
            'filteredTotal'          => $filteredTotal,
            'totalHouseholdsFiltered' => $filteredTotal,
            'perPage'                => $perPage,
            'currentPage'            => $page,
            'filters'                => $filters,
        ]));
    }

    // ── Auth ──────────────────────────────────────────
    public function home()
    {
        return view('index');
    }
    public function login()
    {
        return view('login');
    }
    public function select_role()
    {
        return view('select_role');
    }
    public function create_acc($role = 'resident')
    {
        return view('create_acc', ['role' => ucfirst($role)]);
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
    public function faqs()
    {
        return view('faqs');
    }
    public function privacy_policy()
    {
        return view('privacy_policy');
    }
    public function terms_of_use()
    {
        return view('terms');
    }

    // ── Captain ───────────────────────────────────────
    public function captain_dashboard()
    {
        return redirect()->to('/captain/calendar');
    }
    public function captain_census()
    {
        return $this->_censusView('captain');
    }
    public function captain_household($id = null)
    {
        $householdModel = new \App\Models\HouseholdModel();
        $household      = $householdModel->getWithMembers((string) $id);

        if (! $household) {
            return redirect()->to('/captain/census')->with('error', 'Household not found.');
        }

        return view('dashboard/captain/household', [
            'householdId' => $household['household_no'],
            'household'   => $household,
            'members'     => $household['members'],
            'role'        => 'captain',
        ]);
    }
    public function captain_clearance()
    {
        return view('dashboard/captain/clearance');
    }
    public function captain_clearance_detail($id = '001')
    {
        return view('dashboard/captain/clearance_detail', ['requestId' => $id, 'role' => 'captain']);
    }
    public function captain_reports()
    {
        $db = \Config\Database::connect();

        // ── All persons: heads + members ──────────────────────────────────────
        // Heads have gender; members inherit head's household but have no gender column
        // We use gender from heads table only (members table has no gender field)

        $heads   = $db->table('households')->select('date_of_birth, gender, civil_status, occupation, is_pwd, is_solo_parent, is_4ps, is_senior_citizen, is_indigenous, monthly_income')->get()->getResultArray();
        $members = $db->table('household_members')->select('date_of_birth, occupation, monthly_income')->get()->getResultArray();

        // ── Helper: age from date_of_birth ────────────────────────────────────
        $age = function (?string $dob): ?int {
            if (empty($dob)) return null;
            try {
                return (int) date_diff(date_create($dob), date_create('today'))->y;
            } catch (\Throwable $e) {
                return null;
            }
        };

        // ── Age bracket table (F. Population by Age Bracket) ─────────────────
        // Brackets match the physical form:
        // 1. Children 0–5   2. Children 6–12   3. Children 13–17
        // 4. Adult 18–35    5. Adult 36–50      6. Adult 51–65    7. Adult 66+
        $brackets = [
            ['label' => 'Children 0 – 5 years old',    'min' => 0,  'max' => 5],
            ['label' => 'Children 6 – 12 years old',   'min' => 6,  'max' => 12],
            ['label' => 'Children 13 – 17 years old',  'min' => 13, 'max' => 17],
            ['label' => 'Adult 18 – 35 years old',     'min' => 18, 'max' => 35],
            ['label' => 'Adult 36 – 50 years old',     'min' => 36, 'max' => 50],
            ['label' => 'Adult 51 – 65 years old',     'min' => 51, 'max' => 65],
            ['label' => 'Adult 66 years old & above',  'min' => 66, 'max' => 999],
        ];

        $ageBrackets = [];
        foreach ($brackets as $b) {
            $ageBrackets[] = ['label' => $b['label'], 'male' => 0, 'female' => 0, 'total' => 0];
        }

        // Count heads (have gender)
        foreach ($heads as $h) {
            $a = $age($h['date_of_birth']);
            if ($a === null) continue;
            foreach ($brackets as $i => $b) {
                if ($a >= $b['min'] && $a <= $b['max']) {
                    $g = strtolower($h['gender'] ?? 'male');
                    if ($g === 'female') $ageBrackets[$i]['female']++;
                    else                 $ageBrackets[$i]['male']++;
                    $ageBrackets[$i]['total']++;
                    break;
                }
            }
        }

        // Count members (no gender — count as unknown, add to total only)
        // Members don't have gender in the schema, so we can only add to total
        foreach ($members as $m) {
            $a = $age($m['date_of_birth']);
            if ($a === null) continue;
            foreach ($brackets as $i => $b) {
                if ($a >= $b['min'] && $a <= $b['max']) {
                    $ageBrackets[$i]['total']++;
                    break;
                }
            }
        }

        // ── Sector table (G. Population by Sector) ────────────────────────────
        // Only heads have the boolean flags; members counted by occupation keyword
        $laborForce = $unemployed = $osy = $osc = $pwd = $ofw = $soloParent = $indigenous = 0;
        $civilSingle = $civilMarried = 0;
        $totalPop = count($heads) + count($members);

        foreach ($heads as $h) {
            $occ = strtolower($h['occupation'] ?? '');
            if (! empty($occ) && $occ !== 'none' && $occ !== 'n/a') $laborForce++;
            if (str_contains($occ, 'unemploy'))                       $unemployed++;
            if ($h['is_pwd'])         $pwd++;
            if ($h['is_solo_parent']) $soloParent++;
            if ($h['is_4ps']) {
            } // 4Ps not in sector table
            if ($h['is_indigenous'])  $indigenous++;
            $cs = strtolower($h['civil_status'] ?? '');
            if ($cs === 'single')  $civilSingle++;
            if ($cs === 'married') $civilMarried++;
        }

        // OSY: Out-of-School Youth 15–24 (members only, no occupation)
        foreach ($members as $m) {
            $a   = $age($m['date_of_birth']);
            $occ = strtolower($m['occupation'] ?? '');
            if ($a !== null && $a >= 15 && $a <= 24 && (empty($occ) || $occ === 'none')) $osy++;
            if ($a !== null && $a >= 6  && $a <= 14  && (empty($occ) || $occ === 'none')) $osc++;
            if (! empty($occ) && $occ !== 'none' && $occ !== 'n/a') $laborForce++;
            if (str_contains($occ, 'unemploy')) $unemployed++;
            if (str_contains($occ, 'ofw') || str_contains($occ, 'overseas')) $ofw++;
        }

        $sectorRows = [
            ['label' => 'Labor Force',                              'total' => $laborForce],
            ['label' => 'Unemployed',                               'total' => $unemployed],
            ['label' => 'Out-of-School Youth (OSY) 15–24 y/o',     'total' => $osy],
            ['label' => 'Out-of-School Children (OSC) 6–14 y/o',   'total' => $osc],
            ['label' => 'Persons with Disabilities (PWDs)',         'total' => $pwd],
            ['label' => 'Overseas Filipino Workers (OFWs)',         'total' => $ofw],
            ['label' => 'Solo Parents',                             'total' => $soloParent],
            ['label' => 'Indigenous Peoples (IPs)',                 'total' => $indigenous],
            ['label' => 'Civil Status: Single',                     'total' => $civilSingle],
            ['label' => 'Civil Status: Married',                    'total' => $civilMarried],
        ];

        // ── Summary stats ─────────────────────────────────────────────────────
        $totalHouseholds = count($heads);
        $totalMale       = array_sum(array_column($ageBrackets, 'male'));
        $totalFemale     = array_sum(array_column($ageBrackets, 'female'));

        // Clearances
        $totalClearances = $db->table('clearance_requests')->where('status', 'approved')->countAllResults();

        $avgHHSize = $totalHouseholds > 0
            ? round($totalPop / $totalHouseholds, 1)
            : 0;

        // ── Extra demographic fields ──────────────────────────────────────────
        $registeredVoters = $db->table('households')->where('registered_voter', 1)->countAllResults();
        $totalFamilies    = (int) $db->query("SELECT COALESCE(SUM(num_families),0) AS t FROM households")->getRow()->t;

        return view('dashboard/captain/reports', [
            'totalPop'         => $totalPop,
            'totalMale'        => $totalMale,
            'totalFemale'      => $totalFemale,
            'totalHouseholds'  => $totalHouseholds,
            'totalClearances'  => $totalClearances,
            'avgHHSize'        => $avgHHSize,
            'ageBrackets'      => $ageBrackets,
            'sectorRows'       => $sectorRows,
            'registeredVoters' => $registeredVoters,
            'totalFamilies'    => $totalFamilies,
        ]);
    }
    public function captain_chatbot()
    {
        return view('dashboard/captain/chatbot');
    }
    public function captain_blotter()
    {
        return view('dashboard/captain/blotter');
    }
    public function captain_blotter_detail($id = 'BL-001')
    {
        return view('dashboard/captain/blotter_detail', ['blotterId' => $id, 'role' => 'captain']);
    }
    public function captain_settings()
    {
        return view('dashboard/captain/settings');
    }

    public function captain_create_account()
    {
        return view('dashboard/captain/create_account');
    }

    // ── Secretary ─────────────────────────────────────
    public function secretary_dashboard()
    {
        return redirect()->to('/secretary/calendar');
    }
    public function secretary_census()
    {
        return $this->_censusView('secretary');
    }
    public function secretary_household($id = null)
    {
        $householdModel = new \App\Models\HouseholdModel();
        $household      = $householdModel->getWithMembers((string) $id);

        if (! $household) {
            return redirect()->to('/secretary/census')->with('error', 'Household not found.');
        }

        return view('dashboard/captain/household', [
            'householdId' => $household['household_no'],
            'household'   => $household,
            'members'     => $household['members'],
            'role'        => 'secretary',
        ]);
    }
    public function secretary_clearance()
    {
        return view('dashboard/secretary/clearance');
    }
    public function secretary_clearance_detail($id = '001')
    {
        return view('dashboard/captain/clearance_detail', ['requestId' => $id, 'role' => 'secretary']);
    }
    public function secretary_requests()
    {
        return view('dashboard/secretary/requests');
    }
    public function secretary_reports()
    {
        $db = \Config\Database::connect();

        $heads   = $db->table('households')->select('date_of_birth, gender, civil_status, occupation, is_pwd, is_solo_parent, is_4ps, is_senior_citizen, is_indigenous, monthly_income')->get()->getResultArray();
        $members = $db->table('household_members')->select('date_of_birth, occupation, monthly_income')->get()->getResultArray();

        $age = function (?string $dob): ?int {
            if (empty($dob)) return null;
            try {
                return (int) date_diff(date_create($dob), date_create('today'))->y;
            } catch (\Throwable $e) {
                return null;
            }
        };

        $brackets = [
            ['label' => 'Children 0 – 5 years old',    'min' => 0,  'max' => 5],
            ['label' => 'Children 6 – 12 years old',   'min' => 6,  'max' => 12],
            ['label' => 'Children 13 – 17 years old',  'min' => 13, 'max' => 17],
            ['label' => 'Adult 18 – 35 years old',     'min' => 18, 'max' => 35],
            ['label' => 'Adult 36 – 50 years old',     'min' => 36, 'max' => 50],
            ['label' => 'Adult 51 – 65 years old',     'min' => 51, 'max' => 65],
            ['label' => 'Adult 66 years old & above',  'min' => 66, 'max' => 999],
        ];

        $ageBrackets = [];
        foreach ($brackets as $b) {
            $ageBrackets[] = ['label' => $b['label'], 'male' => 0, 'female' => 0, 'total' => 0];
        }

        foreach ($heads as $h) {
            $a = $age($h['date_of_birth']);
            if ($a === null) continue;
            foreach ($brackets as $i => $b) {
                if ($a >= $b['min'] && $a <= $b['max']) {
                    $g = strtolower($h['gender'] ?? 'male');
                    if ($g === 'female') $ageBrackets[$i]['female']++;
                    else                 $ageBrackets[$i]['male']++;
                    $ageBrackets[$i]['total']++;
                    break;
                }
            }
        }

        foreach ($members as $m) {
            $a = $age($m['date_of_birth']);
            if ($a === null) continue;
            foreach ($brackets as $i => $b) {
                if ($a >= $b['min'] && $a <= $b['max']) {
                    $ageBrackets[$i]['total']++;
                    break;
                }
            }
        }

        $laborForce = $unemployed = $osy = $osc = $pwd = $ofw = $soloParent = $indigenous = 0;
        $civilSingle = $civilMarried = 0;
        $totalPop = count($heads) + count($members);

        foreach ($heads as $h) {
            $occ = strtolower($h['occupation'] ?? '');
            if (! empty($occ) && $occ !== 'none' && $occ !== 'n/a') $laborForce++;
            if (str_contains($occ, 'unemploy'))                       $unemployed++;
            if ($h['is_pwd'])         $pwd++;
            if ($h['is_solo_parent']) $soloParent++;
            if ($h['is_indigenous'])  $indigenous++;
            $cs = strtolower($h['civil_status'] ?? '');
            if ($cs === 'single')  $civilSingle++;
            if ($cs === 'married') $civilMarried++;
        }

        foreach ($members as $m) {
            $a   = $age($m['date_of_birth']);
            $occ = strtolower($m['occupation'] ?? '');
            if ($a !== null && $a >= 15 && $a <= 24 && (empty($occ) || $occ === 'none')) $osy++;
            if ($a !== null && $a >= 6  && $a <= 14  && (empty($occ) || $occ === 'none')) $osc++;
            if (! empty($occ) && $occ !== 'none' && $occ !== 'n/a') $laborForce++;
            if (str_contains($occ, 'unemploy')) $unemployed++;
            if (str_contains($occ, 'ofw') || str_contains($occ, 'overseas')) $ofw++;
        }

        $sectorRows = [
            ['label' => 'Labor Force',                              'total' => $laborForce],
            ['label' => 'Unemployed',                               'total' => $unemployed],
            ['label' => 'Out-of-School Youth (OSY) 15–24 y/o',     'total' => $osy],
            ['label' => 'Out-of-School Children (OSC) 6–14 y/o',   'total' => $osc],
            ['label' => 'Persons with Disabilities (PWDs)',         'total' => $pwd],
            ['label' => 'Overseas Filipino Workers (OFWs)',         'total' => $ofw],
            ['label' => 'Solo Parents',                             'total' => $soloParent],
            ['label' => 'Indigenous Peoples (IPs)',                 'total' => $indigenous],
            ['label' => 'Civil Status: Single',                     'total' => $civilSingle],
            ['label' => 'Civil Status: Married',                    'total' => $civilMarried],
        ];

        $totalHouseholds = count($heads);
        $totalMale       = array_sum(array_column($ageBrackets, 'male'));
        $totalFemale     = array_sum(array_column($ageBrackets, 'female'));
        $totalClearances = $db->table('clearance_requests')->where('status', 'approved')->countAllResults();
        $avgHHSize       = $totalHouseholds > 0 ? round($totalPop / $totalHouseholds, 1) : 0;

        // ── Extra demographic fields ──────────────────────────────────────────
        $registeredVoters = $db->table('households')->where('registered_voter', 1)->countAllResults();
        $totalFamilies    = (int) $db->query("SELECT COALESCE(SUM(num_families),0) AS t FROM households")->getRow()->t;

        return view('dashboard/secretary/reports', [
            'totalPop'         => $totalPop,
            'totalMale'        => $totalMale,
            'totalFemale'      => $totalFemale,
            'totalHouseholds'  => $totalHouseholds,
            'totalClearances'  => $totalClearances,
            'avgHHSize'        => $avgHHSize,
            'ageBrackets'      => $ageBrackets,
            'sectorRows'       => $sectorRows,
            'registeredVoters' => $registeredVoters,
            'totalFamilies'    => $totalFamilies,
        ]);
    }
    public function secretary_chatbot()
    {
        return view('dashboard/secretary/chatbot');
    }
    public function secretary_blotter()
    {
        return view('dashboard/secretary/blotter');
    }
    public function secretary_blotter_detail($id = 'BL-001')
    {
        return view('dashboard/captain/blotter_detail', ['blotterId' => $id, 'role' => 'secretary']);
    }
    public function secretary_settings()
    {
        $userModel = new \App\Models\UserModel();
        $users = $userModel
            ->select('id, full_name, username, role')
            ->whereIn('role', ['captain', 'secretary', 'treasurer', 'resident', 'sk'])
            ->where('status', 'active')
            ->orderBy('full_name', 'ASC')
            ->findAll();

        return view('dashboard/secretary/settings', ['allUsers' => $users]);
    }
    public function secretary_create_account()
    {
        $userModel = new \App\Models\UserModel();
        return view('dashboard/secretary/create_account', [
            'activeCaptain'   => $userModel->getActiveByRole('captain'),
            'activeSecretary' => $userModel->getActiveByRole('secretary'),
        ]);
    }

    // ── Treasurer ─────────────────────────────────────
    public function treasurer_dashboard()
    {
        return view('dashboard/treasurer');
    }
    public function treasurer_payments()
    {
        return view('dashboard/treasurer/payments');
    }
    public function treasurer_clearance()
    {
        return view('dashboard/treasurer/clearance');
    }
    public function treasurer_reports()
    {
        return view('dashboard/treasurer/reports');
    }
    public function treasurer_settings()
    {
        return view('dashboard/treasurer/settings');
    }

    // ── Resident ──────────────────────────────────────
    public function resident_dashboard()
    {
        $userId = (int) session()->get('user_id');

        // Clearance stats
        $clearanceModel = new \App\Models\ClearanceRequestModel();
        $allRequests    = $clearanceModel->getByUser($userId);

        $totalRequests  = count($allRequests);
        $approved       = count(array_filter($allRequests, fn($r) => $r['status'] === 'approved'));
        $pending        = count(array_filter($allRequests, fn($r) => $r['status'] === 'pending'));

        // Blotter cases filed by this resident
        $db            = \Config\Database::connect();
        $blotterCount  = (int) $db->table('blotter_reports')
            ->where('complainant_user_id', $userId)
            ->countAllResults();

        // 5 most recent requests for the dashboard table
        $recentRequests = array_slice($allRequests, 0, 5);

        return view('dashboard/resident', [
            'totalRequests'  => $totalRequests,
            'approved'       => $approved,
            'pending'        => $pending,
            'blotterCount'   => $blotterCount,
            'recentRequests' => $recentRequests,
        ]);
    }
    public function resident_clearance()
    {
        return view('dashboard/resident/clearance');
    }
    public function resident_profile()
    {
        $userId  = session()->get('user_id');
        $userModel = new \App\Models\UserModel();
        $user    = $userModel->find($userId);

        $household = null;
        $members   = [];
        $memberRecord = null;

        if (! empty($user['household_no'])) {
            $householdModel = new \App\Models\HouseholdModel();
            $household      = $householdModel->find($user['household_no']);

            if ($household) {
                $memberModel = new \App\Models\HouseholdMemberModel();
                $members     = $memberModel->where('household_no', $user['household_no'])->findAll();

                // Find this resident's own member record by name match
                $fullName = strtoupper(trim($user['full_name']));
                foreach ($members as $m) {
                    $mName = strtoupper(trim($m['first_name'] . ' ' . $m['last_name']));
                    $mNameAlt = strtoupper(trim($m['last_name'] . ' ' . $m['first_name']));
                    if ($fullName === $mName || $fullName === $mNameAlt) {
                        $memberRecord = $m;
                        break;
                    }
                }

                // If not found as member, check if they are the household head
                if (! $memberRecord) {
                    $headName = strtoupper(trim($household['first_name'] . ' ' . $household['last_name']));
                    if ($fullName === $headName) {
                        $memberRecord = $household; // use head data
                    }
                }
            }
        }

        return view('dashboard/resident/profile', [
            'user'         => $user,
            'household'    => $household,
            'members'      => $members,
            'memberRecord' => $memberRecord,
        ]);
    }
    public function resident_chatbot()
    {
        return view('dashboard/resident/chatbot');
    }
    public function resident_notifications()
    {
        return view('dashboard/resident/notifications');
    }

    // ── SK ────────────────────────────────────────────────
    public function sk_dashboard()
    {
        return view('dashboard/sk');
    }
    public function sk_profiling()
    {
        return view('dashboard/sk/profiling');
    }
    public function sk_household($id = null)
    {
        $householdModel = new \App\Models\HouseholdModel();
        $household      = $householdModel->getWithMembers((string) $id);

        if (! $household) {
            return redirect()->to('/sk/profiling')->with('error', 'Household not found.');
        }

        // Find the youth member (15–30) that was clicked from the profiling list
        // The profiling list passes the member's source and id via query string
        $memberSource = $_GET['source'] ?? 'head';
        $memberId     = (int) ($_GET['member_id'] ?? 0);

        $youthMember = null;
        if ($memberSource === 'member' && $memberId > 0) {
            // Find the specific member
            foreach ($household['members'] as $m) {
                if ((int) $m['id'] === $memberId) {
                    $youthMember = $m;
                    break;
                }
            }
        } elseif ($memberSource === 'head') {
            // The head is the youth
            $youthMember = array_merge($household, ['relationship' => 'Household Head', 'id' => null]);
        }

        return view('dashboard/sk/household', [
            'householdId' => $household['household_no'],
            'household'   => $household,
            'members'     => $household['members'],
            'youthMember' => $youthMember,
            'role'        => 'sk',
        ]);
    }
    public function sk_add_youth()
    {
        return view('dashboard/sk/add_youth');
    }
    public function sk_programs()
    {
        return view('dashboard/sk/programs');
    }
    public function sk_reports()
    {
        return view('dashboard/sk/reports');
    }
    public function sk_settings()
    {
        return view('dashboard/sk/settings');
    }
}
