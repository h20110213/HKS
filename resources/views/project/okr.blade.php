@extends('layouts.master')
@section('title','專案OKR')
@section('content')
@include('okrs.list', ['actionlist'=>false, 'admin'=>$owner->user_id, 'routeObjectiveStore' =>
route('project.objective.store', $owner->id)])
@endsection