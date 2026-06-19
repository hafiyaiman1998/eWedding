<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DesignTemplate;
use App\Models\User;
use App\Models\WeddingCard;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user first
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@eweddingcard.com',
            'password' => Hash::make('password123'),
            'type' => 'admin',
        ]);

        $this->command->info('Created admin user: admin@eweddingcard.com (password: password123)');

        // Create sample design templates
        $template1 = DesignTemplate::create([
            'name' => 'Traditional Malaysian Songket',
            'description' => 'Beautiful traditional Malaysian design featuring songket patterns and gold accents',
            'blade_template' => '<div class="malaysian-songket-card">
    <div class="songket-header">
        <div class="gold-pattern"></div>
        <h1 class="couple-names">{{ $details["bride_name"] }} & {{ $details["groom_name"] }}</h1>
        <div class="gold-pattern"></div>
    </div>
    <div class="wedding-details">
        <p class="invitation-text">Dengan segala hormatnya, kami menjemput Dato\'/Datin/Tuan/Puan untuk menghadiri majlis perkahwinan kami</p>
        <div class="date-venue">
            <p><strong>Tarikh:</strong> {{ $details["wedding_date"] }}</p>
            <p><strong>Masa:</strong> {{ $details["wedding_time"] ?? "10:00 Pagi" }}</p>
            <p><strong>Tempat:</strong> {{ $details["venue"] ?? "Dewan Komuniti" }}</p>
            <p><strong>Alamat:</strong> {{ $details["address"] ?? "Kuala Lumpur" }}</p>
        </div>
        <div class="contact-info">
            <p>Hubungi: {{ $details["contact_bride"] ?? "012-3456789" }} / {{ $details["contact_groom"] ?? "012-9876543" }}</p>
        </div>
    </div>
</div>

<style>
.malaysian-songket-card {
    background: linear-gradient(135deg, #8B4513 0%, #DAA520 100%);
    padding: 40px;
    border-radius: 15px;
    color: white;
    text-align: center;
    font-family: "Times New Roman", serif;
    max-width: 600px;
    margin: 0 auto;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}
.songket-header { margin-bottom: 30px; }
.gold-pattern { height: 3px; background: #FFD700; margin: 10px 0; }
.couple-names { font-size: 2.5rem; margin: 20px 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
.invitation-text { font-style: italic; margin: 20px 0; font-size: 1.1rem; }
.date-venue p { margin: 10px 0; font-size: 1.1rem; }
.contact-info { margin-top: 20px; font-size: 0.9rem; }
</style>',
            'category' => 'malaysian',
            'is_malaysian_design' => true,
            'default_variables' => [
                'bride_name' => 'Siti Aisyah',
                'groom_name' => 'Ahmad Rahman',
                'wedding_date' => '15 Ogos 2024',
                'wedding_time' => '10:00 Pagi',
                'venue' => 'Dewan Serbaguna Taman Melawati',
                'address' => 'Jalan Melawati 1, 53100 Kuala Lumpur',
                'contact_bride' => '012-3456789',
                'contact_groom' => '012-9876543'
            ]
        ]);

        $template2 = DesignTemplate::create([
            'name' => 'Elegant Batik Design',
            'description' => 'Modern interpretation of traditional batik patterns with contemporary typography',
            'blade_template' => '<div class="batik-card">
    <div class="batik-border">
        <div class="card-content">
            <h1 class="title">Jemputan Majlis Perkahwinan</h1>
            <div class="couple-section">
                <h2 class="bride-name">{{ $details["bride_name"] }}</h2>
                <div class="ampersand">&</div>
                <h2 class="groom-name">{{ $details["groom_name"] }}</h2>
            </div>
            <div class="details-section">
                <p class="date">{{ $details["wedding_date"] }}</p>
                <p class="time">{{ $details["wedding_time"] ?? "2:00 Petang" }}</p>
                <p class="venue">{{ $details["venue"] ?? "Dewan Bandaraya" }}</p>
            </div>
            <div class="custom-message">
                {{ $details["custom_message"] ?? "Kehadiran anda amat dihargai" }}
            </div>
        </div>
    </div>
</div>

<style>
.batik-card {
    background: #F5F5DC;
    padding: 20px;
    border-radius: 10px;
    max-width: 500px;
    margin: 0 auto;
    font-family: "Georgia", serif;
}
.batik-border {
    border: 3px solid #8B4513;
    border-radius: 8px;
    padding: 30px;
    background: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(139, 69, 19, 0.1) 10px, rgba(139, 69, 19, 0.1) 20px);
}
.title { color: #8B4513; text-align: center; margin-bottom: 20px; font-size: 1.8rem; }
.couple-section { text-align: center; margin: 30px 0; }
.bride-name, .groom-name { color: #B8860B; font-size: 2rem; margin: 10px 0; }
.ampersand { font-size: 1.5rem; color: #8B4513; margin: 10px 0; }
.details-section { text-align: center; margin: 25px 0; }
.details-section p { margin: 8px 0; color: #654321; font-size: 1.1rem; }
.custom-message { text-align: center; font-style: italic; color: #8B4513; margin-top: 20px; }
</style>',
            'category' => 'malaysian',
            'is_malaysian_design' => true,
            'default_variables' => [
                'bride_name' => 'Fatimah',
                'groom_name' => 'Muhammad',
                'wedding_date' => '20 September 2024',
                'wedding_time' => '2:00 Petang',
                'venue' => 'Dewan Bandaraya Kuala Lumpur'
            ]
        ]);

        $template3 = DesignTemplate::create([
            'name' => 'Modern Minimalist',
            'description' => 'Clean and modern design with elegant typography and subtle gradients',
            'blade_template' => '<div class="modern-card">
    <div class="header-section">
        <h1 class="invitation-title">You\'re Invited</h1>
        <div class="divider"></div>
    </div>
    <div class="couple-names">
        <h2 class="bride">{{ $details["bride_name"] }}</h2>
        <span class="and">&</span>
        <h2 class="groom">{{ $details["groom_name"] }}</h2>
    </div>
    <div class="event-details">
        <p class="event-type">Wedding Celebration</p>
        <p class="date">{{ $details["wedding_date"] }}</p>
        <p class="time">{{ $details["wedding_time"] ?? "6:00 PM" }}</p>
        <p class="venue">{{ $details["venue"] ?? "The Grand Ballroom" }}</p>
    </div>
    @if(isset($details["custom_message"]))
    <div class="message">
        {{ $details["custom_message"] }}
    </div>
    @endif
</div>

<style>
.modern-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 50px 40px;
    border-radius: 20px;
    text-align: center;
    max-width: 500px;
    margin: 0 auto;
    font-family: "Arial", sans-serif;
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}
.invitation-title { font-size: 1.5rem; margin-bottom: 20px; font-weight: 300; }
.divider { width: 50px; height: 2px; background: white; margin: 0 auto 30px; }
.couple-names { margin: 40px 0; }
.bride, .groom { font-size: 2.5rem; margin: 10px 0; font-weight: 700; }
.and { font-size: 1.5rem; font-style: italic; }
.event-details { margin: 40px 0; }
.event-details p { margin: 15px 0; font-size: 1.1rem; }
.event-type { font-weight: 600; text-transform: uppercase; letter-spacing: 2px; }
.message { margin-top: 30px; font-style: italic; opacity: 0.9; }
</style>',
            'category' => 'modern',
            'is_malaysian_design' => false,
            'default_variables' => [
                'bride_name' => 'Sarah',
                'groom_name' => 'John',
                'wedding_date' => 'July 15, 2024',
                'wedding_time' => '6:00 PM',
                'venue' => 'The Grand Ballroom'
            ]
        ]);

        // Create sample clients
        $client1 = User::create([
            'name' => 'Siti Nurhaliza Abdullah',
            'email' => 'siti.nurhaliza@email.com',
            'password' => Hash::make('password123'),
            'type' => 'user'
        ]);

        $client2 = User::create([
            'name' => 'Ahmad Rahman Ismail',
            'email' => 'ahmad.rahman@email.com',
            'password' => Hash::make('password123'),
            'type' => 'user'
        ]);

        $client3 = User::create([
            'name' => 'Fatimah Zahra Mohd',
            'email' => 'fatimah.zahra@email.com',
            'password' => Hash::make('password123'),
            'type' => 'user'
        ]);

        $client4 = User::create([
            'name' => 'Sarah Michelle Johnson',
            'email' => 'sarah.johnson@email.com',
            'password' => Hash::make('password123'),
            'type' => 'user'
        ]);

        $client5 = User::create([
            'name' => 'John Davidson Smith',
            'email' => 'john.smith@email.com',
            'password' => Hash::make('password123'),
            'type' => 'user'
        ]);

        // Create sample wedding cards using the created templates and clients
        WeddingCard::create([
            'user_id' => $client1->id,
            'design_template_id' => $template1->id,
            'title' => 'Siti & Ahmad Wedding Invitation',
            'card_details' => [
                'bride_name' => 'Siti Nurhaliza',
                'groom_name' => 'Ahmad Rahman',
                'wedding_date' => '15 Ogos 2024',
                'wedding_time' => '10:00 Pagi',
                'venue' => 'Dewan Serbaguna Taman Melawati',
                'address' => 'Jalan Melawati 1, 53100 Kuala Lumpur',
                'contact_bride' => '012-3456789',
                'contact_groom' => '012-9876543'
            ],
            'custom_message' => 'Dengan segala hormatnya, kami menjemput kehadiran Dato\'/Datin/Tuan/Puan untuk berkongsi kegembiraan bersama kami.',
            'is_published' => true,
            'unique_url' => 'siti-ahmad-2024'
        ]);

        WeddingCard::create([
            'user_id' => $client2->id,
            'design_template_id' => $template2->id,
            'title' => 'Fatimah & Muhammad Wedding',
            'card_details' => [
                'bride_name' => 'Fatimah Zahra',
                'groom_name' => 'Muhammad Hafiz',
                'wedding_date' => '20 September 2024',
                'wedding_time' => '2:00 Petang',
                'venue' => 'Dewan Bandaraya Kuala Lumpur',
                'address' => 'Jalan Raja, 50050 Kuala Lumpur',
                'contact_bride' => '019-8765432',
                'contact_groom' => '017-2345678'
            ],
            'custom_message' => 'Kehadiran anda amat dihargai untuk menyaksikan detik bahagia kami.',
            'is_published' => false,
            'unique_url' => 'fatimah-muhammad-2024'
        ]);

        WeddingCard::create([
            'user_id' => $client3->id,
            'design_template_id' => $template3->id,
            'title' => 'Sarah & John Modern Wedding',
            'card_details' => [
                'bride_name' => 'Sarah Michelle',
                'groom_name' => 'John Davidson',
                'wedding_date' => 'November 12, 2024',
                'wedding_time' => '6:00 PM',
                'venue' => 'The Grand Ballroom',
                'address' => 'Shangri-La Hotel, Kuala Lumpur',
                'contact_bride' => '012-1234567',
                'contact_groom' => '012-7654321'
            ],
            'custom_message' => 'Join us for an evening of love, laughter, and happily ever after.',
            'is_published' => true,
            'unique_url' => 'sarah-john-2024'
        ]);

        // Create a few more sample cards for better demonstration
        WeddingCard::create([
            'user_id' => $client4->id,
            'design_template_id' => $template1->id,
            'title' => 'Malaysian Traditional Wedding - Draft',
            'card_details' => [
                'bride_name' => 'Aminah Binti Abdullah',
                'groom_name' => 'Razak Bin Hassan',
                'wedding_date' => '5 Disember 2024',
                'wedding_time' => '11:00 Pagi',
                'venue' => 'Masjid Negara',
                'address' => 'Jalan Perdana, 50480 Kuala Lumpur',
                'contact_bride' => '013-4567890',
                'contact_groom' => '014-5678901'
            ],
            'custom_message' => 'Dengan penuh kesyukuran, kami mengundang kehadiran anda.',
            'is_published' => false,
            'unique_url' => 'aminah-razak-2024'
        ]);

        WeddingCard::create([
            'user_id' => $client5->id,
            'design_template_id' => $template2->id,
            'title' => 'Elegant Batik Wedding Celebration',
            'card_details' => [
                'bride_name' => 'Nurul Ain',
                'groom_name' => 'Farid Hakim',
                'wedding_date' => '25 Januari 2025',
                'wedding_time' => '7:00 Malam',
                'venue' => 'Grand Hyatt Kuala Lumpur',
                'address' => 'Jalan Pinang, 50450 Kuala Lumpur',
                'contact_bride' => '016-7890123',
                'contact_groom' => '018-9012345'
            ],
            'custom_message' => 'Satu detik kebahagiaan untuk dikongsi bersama.',
            'is_published' => true,
            'unique_url' => 'nurul-farid-2025'
        ]);

        $this->command->info('Sample data created successfully!');
        $this->command->info('Created:');
        $this->command->info('- 1 Admin User (admin@eweddingcard.com)');
        $this->command->info('- 3 Design Templates (2 Malaysian, 1 Modern)');
        $this->command->info('- 5 Sample Clients');
        $this->command->info('- 5 Wedding Cards (3 Published, 2 Draft)');
        $this->command->info('');
        $this->command->info('You can now login with:');
        $this->command->info('Email: admin@eweddingcard.com');
        $this->command->info('Password: password123');
    }
}
