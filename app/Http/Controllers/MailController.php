<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Mail;
use App\Mail\HelloMail;
use App\Mail\WelcomePro;
use App\Mail\WelcomeBarathonien;
use App\Mail\ChangePassword;
use App\Mail\ValidePro;
use App\Mail\RefusePro;
use App\Mail\ValideEstablishmentPro;
use App\Mail\RefuseEstablishmentPro;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MailController extends Controller
{
    use HttpResponses;

    public function hello(){
        Mail::to("barathon.m2i@gmail.com")->send(new HelloMail("test"));

        return $this->success(null, "MAIL SEND");

    }

    public function welcomePro($id){

        $user = User::findOrFail($id);

        Mail::to("barathon.m2i@gmail.com")->send(new WelcomePro($user));

        return $this->success(null, "MAIL SEND");

    }

    public function welcomeBarathonien($id){

        $user = User::findOrFail($id);

        Mail::to("barathon.m2i@gmail.com")->send(new WelcomeBarathonien($user));

        return $this->success(null, "MAIL SEND");

    }

    public function changePassword($id){

        $user = User::findOrFail($id);
        $newPassword = Str::random(10);
        $user->password = Hash::make($newPassword);

        Mail::to("barathon.m2i@gmail.com")->send(new ChangePassword($user, $newPassword));
        return $this->success(null, "MAIL SEND");

    }

    public function statusPro($id, $status){

        $user = User::findOrFail($id);

        if($status == '0'){
            Mail::to("barathon.m2i@gmail.com")->send(new ValidePro($user));
        }else if($status == '1'){
            Mail::to("barathon.m2i@gmail.com")->send(new RefusePro($user));
        }

        return $this->success(null, "MAIL SEND");

    }

    public function statusEstablishmentPro($id, $status){
        $establishment = Establishment::findOrFail($id);
        $owner = $establishment->owner;
        $user = $owner->users[0];

        if($status == '0'){
            Mail::to("barathon.m2i@gmail.com")->send(new ValideEstablishmentPro($user, $establishment));
        }else if($status == '1'){
            Mail::to("barathon.m2i@gmail.com")->send(new RefuseEstablishmentPro($user, $establishment));
        }

        return $this->success(null, "MAIL SEND");

    }
}
