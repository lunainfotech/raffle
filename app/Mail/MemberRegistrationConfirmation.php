<?php

namespace App\Mail;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemberRegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $member;

    public function __construct( $members , $tototalCharge)
    {
        $this->members = $members;
    }

    public function build()
    {
        $email = $this->subject('Shree Ram Rath Raffle Ticket Confirmation')->view('emails.member_registration_confirmation', [
            'members' => $this->members
        ]);

        foreach ($this->members as $member) {
            $email->attach(storage_path('app/public/raffle_cards/' . $member->membership_number . '.png'), [
                'as' => $member->membership_number . '.png',
                'mime' => 'image/png',
            ]);
        }

        return $email;
    }
}
