<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ChatbotFaqSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $faqs = [
            [
                'category'   => 'office_hours',
                'question'   => 'What are the barangay office hours?',
                'keywords'   => 'office hours, schedule, oras, bukas, open, sarado, barangay hall',
                'answer'     => 'The barangay office is usually open during regular working hours. Please contact the barangay office directly to confirm the exact schedule for the day.',
                'language'   => 'mixed',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category'   => 'barangay_clearance',
                'question'   => 'What are the requirements for barangay clearance?',
                'keywords'   => 'barangay clearance, clearance requirements, requirements, kailangan, kuha clearance',
                'answer'     => 'For barangay clearance, please prepare a valid ID and any barangay-required supporting details. Approval and final requirements must be confirmed by the barangay office.',
                'language'   => 'mixed',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category'   => 'certificate_residency',
                'question'   => 'What are the requirements for certificate of residency?',
                'keywords'   => 'certificate of residency, residency, proof of residence, certificate, residente',
                'answer'     => 'For a Certificate of Residency, residents usually need to provide identification and confirm their residency details. Please verify the final requirements with the barangay office.',
                'language'   => 'mixed',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category'   => 'certificate_indigency',
                'question'   => 'What are the requirements for certificate of indigency?',
                'keywords'   => 'certificate of indigency, indigency, indigent, financial assistance, mahirap, ayuda',
                'answer'     => 'For a Certificate of Indigency, the barangay may need to verify the resident’s situation and supporting information. The chatbot cannot decide eligibility. Please contact the barangay office for assessment.',
                'language'   => 'mixed',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category'   => 'good_moral',
                'question'   => 'How can I request a good moral certificate?',
                'keywords'   => 'good moral, certificate of good moral, moral certificate, request good moral',
                'answer'     => 'You may request a Good Moral Certificate through the barangay office or the system if the feature is available. The barangay staff will still verify and process the request.',
                'language'   => 'mixed',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category'   => 'first_time_jobseeker',
                'question'   => 'How can I request a first-time job seeker certificate?',
                'keywords'   => 'first time job seeker, first-time jobseeker, job seeker certificate, employment certificate',
                'answer'     => 'For a First-Time Job Seeker Certificate, prepare valid identification and request guidance from the barangay office. The barangay will verify if you qualify before issuing the certificate.',
                'language'   => 'mixed',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category'   => 'announcements',
                'question'   => 'Where can I see barangay announcements?',
                'keywords'   => 'announcement, announcements, notice, updates, balita, anunsyo',
                'answer'     => 'You may check barangay announcements in the system dashboard if available, or contact the barangay office for the latest official updates.',
                'language'   => 'mixed',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category'   => 'appointments',
                'question'   => 'How do I schedule an appointment?',
                'keywords'   => 'appointment, schedule, booking, magpa appointment, appointment scheduling',
                'answer'     => 'Use the appointment scheduling feature if available in your account. Select the service, date, and time, then wait for confirmation from the barangay staff.',
                'language'   => 'mixed',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category'   => 'census',
                'question'   => 'What is the census module for?',
                'keywords'   => 'census, profiling, resident profile, household, population, impormasyon',
                'answer'     => 'The census module helps the barangay maintain updated resident and household information. The chatbot cannot reveal private census records.',
                'language'   => 'mixed',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category'   => 'blotter',
                'question'   => 'How can I ask about blotter or incident reports?',
                'keywords'   => 'blotter, incident, report, complaint, reklamo, sumbong',
                'answer'     => 'For blotter or incident concerns, please contact or visit the barangay office. The chatbot can only give general guidance and cannot provide legal advice or make decisions.',
                'language'   => 'mixed',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category'   => 'account',
                'question'   => 'How do I register or log in?',
                'keywords'   => 'register, login, account, sign up, password, forgot password, rehistro',
                'answer'     => 'Use the system registration or login page. If you cannot access your account, contact the barangay staff or system administrator for help.',
                'language'   => 'mixed',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'category'   => 'fallback_scope',
                'question'   => 'What questions can the chatbot answer?',
                'keywords'   => 'help, chatbot, scope, what can you do, tulong',
                'answer'     => 'I can help with general barangay inquiries, document requirements, schedules, announcements, census guidance, blotter guidance, account help, and basic system navigation. I cannot give legal, medical, financial, political, or unrelated advice.',
                'language'   => 'mixed',
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->db->table('chatbot_faqs')->insertBatch($faqs);
    }
}