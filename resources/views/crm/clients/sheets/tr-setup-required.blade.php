@extends('layouts.crm_client_detail')
@section('title', 'TR Sheet - Setup Required')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/listing-container.css') }}">
@endsection

@section('content')
<div class="listing-container">
    <div class="section-body">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-tools fa-4x text-muted mb-3"></i>
                <h4>TR Sheet – Setup Required</h4>
                <p class="text-muted mb-0">
                    Run the following migrations to create the required tables and columns:
                </p>
                <code class="d-block mt-3 p-3 bg-light rounded text-left">
                    php artisan migrate
                </code>
                <p class="small text-muted mt-3">
                    Migrations: <code>create_client_tr_references_table</code>,
                    <code>add_tr_checklist_status_to_client_matters_table</code>
                </p>
                <a href="{{ route('clients.index') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Clients
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
