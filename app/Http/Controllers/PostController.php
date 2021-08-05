<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PostController extends Controller
{
    public function store(){


        $this->resolveAathorization();

        /*--------------------------*/

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . auth()->user()->accessToken->access_token,
        ])->post('http://apicoders.test/v1/posts', [
            'name' => 'Post de teste',
            'slug' => 'post-de-teste',
            'extract' => 'hfshdfhjhks',
            'body' => 'kdskkjfkjdsfkjsdkfjksdjfjsdlkfjdsklfjs',
            'category_id' => 1
        ]);

        return $response->json();
    }
}
