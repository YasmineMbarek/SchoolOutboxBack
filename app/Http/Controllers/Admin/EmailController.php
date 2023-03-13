<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendEmail(string $customerName,string $adminName, string $customerAddress,string $subject )
    {
        $mailInfo = new \stdClass();
        $mailInfo->recieverName = $customerName; // customer name
        $mailInfo->sender = $adminName; // admin name
        $mailInfo->senderCompany = "school-out-box"; //fix school-out-box
        $mailInfo->to = $customerAddress; //custmer adresse
        $mailInfo->subject = $subject; //change with variable
       /* $mailInfo->cc = "ci@email.com";
        $mailInfo->bcc = "jim@email.com";*/
        $mailInfo->from='mbarekyasmine2017@gmail.com';

        Mail::to($customerAddress)
            ->send(new SendMail($mailInfo));
    }
}
