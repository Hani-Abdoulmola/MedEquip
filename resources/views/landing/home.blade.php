{{-- @extends('layouts.landing')

@section('title', 'MediTrust - منصة المعدات الطبية الرائدة')
@section('meta_description',
    'منصة MediTrust الرائدة في تجارة المعدات والأجهزة الطبية بين الموردين والمؤسسات الطبية في
    العالم العربي. احصل على أفضل المعدات بأسعار تنافسية.')

@section('content') --}}

{{-- =========================
          Hero Section - الواجهة الرئيسية
    ========================== --}}
{{-- @include('components.sections.hero') --}}

{{-- =========================
          About Section - نبذة عن المنصة والمميزات
    ========================== --}}
{{-- @include('components.sections.about') --}}

{{-- =========================
          Services Section - الخدمات وكيفية العمل
    ========================== --}}
{{-- @include('components.sections.services') --}}

{{-- =========================
          Categories Section - أقسام المعدات الطبية
    ========================== --}}
{{-- @include('components.sections.categories') --}}

{{-- =========================
          Team Section - فريق الخبراء ومؤشرات الثقة
    ========================== --}}
{{-- @include('components.sections.team') --}}

{{-- =========================
          Gallery Section - معرض الأعمال والمشاريع
    ========================== --}}
{{-- @include('components.sections.gallery') --}}

{{-- =========================
          FAQ Section - الأسئلة الشائعة
    ========================== --}}
{{-- @include('components.sections.faq') --}}

{{-- =========================
         📞 Contact Section - التواصل والاستفسارات
    ========================== --}}
{{-- @include('components.sections.contact') --}}
{{--
@endsection

@push('scripts')
    <script>
        // Initialize MediTrust App when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // The MediTrustApp is already initialized in modern.js
            console.log('MediTrust Landing Page Loaded Successfully');

            // Add any page-specific JavaScript here

            // Smooth scroll for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        const offsetTop = target.offsetTop - 80; // Account for fixed navbar
                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
@endpush --}}