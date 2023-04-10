<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Mail;
use App\Mail\HelloMail;
use App\Mail\WelcomePro;
use App\Models\Owner;
use App\Models\User;

class MailController extends Controller
{
    use HttpResponses;

    public function hello(){
        Mail::to("barathon.m2i@gmail.com")->send(new HelloMail("test"));

        return $this->success(null, "MAIL SEND");

    }

    public function welcomePro($id){

        $user = User::findOrFail($id);

        $owner = $user->owner;

        Mail::to("barathon.m2i@gmail.com")->send(new WelcomePro($user, $owner));

        return $this->success(null, "MAIL SEND");

    }
}
