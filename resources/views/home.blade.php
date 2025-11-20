@extends('layouts.landing')

@section('title', 'MediEquip - منصة المعدات الطبية الرائدة في العالم العربي')

@section('description',
    'منصة MediEquip الرائدة لربط موردي المعدات الطبية بالمؤسسات الصحية في العالم العربي. حلول
    متكاملة، أسعار تنافسية، وخدمة احترافية.')

@section('content')
    {{-- Hero Section --}}
    @include('sections.hero')

    {{-- About Section --}}
    @include('sections.about')

    {{-- Services Section --}}
    @include('sections.services')

    {{-- Categories Section --}}
    @include('sections.categories')

    {{-- Team Section --}}
    @include('sections.partners')

    {{-- Gallery Section --}}
    @include('sections.gallery')

    {{-- FAQ Section --}}
    @include('sections.faq')

    {{-- Contact Section --}}
    @include('sections.contact')
@endsection
