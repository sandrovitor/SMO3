@extends('layouts.login')

@php
    if(isset($_COOKIE['user']) && $_COOKIE['user'] != '') {
      if(!isset($_SESSION['user'])) {
        $_SESSION['user'] = $_COOKIE['user'];
      }
      $saveuserChecked = 'checked="checked"';
    } else {
      $saveuserChecked = '';
    }

	if(isset($_SESSION['user']) && $_SESSION['user'] != '') {
		$afSenha = 'autofocus="autofocus"';
		$afUser = '';
	} else {
		$afUser = 'autofocus="autofocus"';
		$afSenha = '';
	}

    $msgRetorno = '';
    if($smoMSG != false) {
		  $msgRetorno = $smoMSG;
	  }

    $loginFoto = '';
    switch(rand(0, 5)) {
      case 0:
        $loginFoto = 'images/302016037_univ_lsr_xl.jpg';
        break;

      case 1:
        $loginFoto = 'images/302016044_univ_cnt_2_xl.jpg';
        break;

      case 2:
        $loginFoto = 'images/1102018985_univ_lsr_lg.jpg';
        break;

      case 3:
        $loginFoto = 'images/502016131_univ_lsr_xl.jpg';
        break;

      case 4:
        $loginFoto = 'images/502018510_univ_lsr_xl.jpg';
        break;

      case 5:
        $loginFoto = 'images/202017332_univ_cnt_3_xl.jpg';
        break;
    }

@endphp

@section ('msgRetorno', $msgRetorno)
@section ('loginFoto', $loginFoto)
@section ('saveuserChecked', $saveuserChecked)
@section ('afUser', $afUser)
@section ('afSenha', $afSenha)
