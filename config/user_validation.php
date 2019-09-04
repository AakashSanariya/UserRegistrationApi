<?php

return [

    /*
     * For User Registration Validation
    */

    'Add_User_Validation' => [
        'firstName' => 'required|min:3|alpha_num',
        'lastName' => 'required|min:3|alpha_num',
        'email' => 'required|unique:users|email',
        'image' => 'required|mimes:jpeg,jpg,png',
        'password' => 'required|min:6',
        'confirmPassword' => 'required|same:password|min:6'
    ],

    /*
     * For User Login Check
    */
    'Login_Validation' => [
        'email' => 'required',
        'password' => 'required|min:6'
    ],

    /*
     * For User Information Details Update
    */
    'Update_Validation' => [
        'image' => 'mimes:jpeg,jpg,png'
    ],

];
