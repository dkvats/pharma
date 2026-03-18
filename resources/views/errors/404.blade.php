@extends('layouts.app')

@section('title', 'Page Not Found')
@section('page-title', '404 - Page Not Found')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12 text-center">
    <h1 class="text-4xl font-bold text-gray-900 mb-3">404</h1>
    <p class="text-gray-600 mb-6">The page you are looking for does not exist or has been moved.</p>
    <a href="{{ route('home') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        Go to Home
    </a>
</div>
@endsection
