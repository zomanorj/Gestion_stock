@if (session('success'))
    <div class="alert alert-dismissible fade show mb-3" role="alert" style="background-color: var(--color-success-light); border-left: 3px solid var(--color-success); color: #065f46; border-radius: 8px; padding: 12px 16px; border-top: 1px solid #d1fae5; border-right: 1px solid #d1fae5; border-bottom: 1px solid #d1fae5;">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2" style="color: var(--color-success); font-size: 16px;"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Fermer" style="font-size: 10px; opacity: 0.5;"></button>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-dismissible fade show mb-3" role="alert" style="background-color: var(--color-danger-light); border-left: 3px solid var(--color-danger); color: #991b1b; border-radius: 8px; padding: 12px 16px; border-top: 1px solid #fecaca; border-right: 1px solid #fecaca; border-bottom: 1px solid #fecaca;">
        <div class="d-flex align-items-center">
            <i class="bi bi-x-circle-fill me-2" style="color: var(--color-danger); font-size: 16px;"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Fermer" style="font-size: 10px; opacity: 0.5;"></button>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-dismissible fade show mb-3" role="alert" style="background-color: var(--color-danger-light); border-left: 3px solid var(--color-danger); color: #991b1b; border-radius: 8px; padding: 12px 16px; border-top: 1px solid #fecaca; border-right: 1px solid #fecaca; border-bottom: 1px solid #fecaca;">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-circle-fill me-2" style="color: var(--color-danger); font-size: 16px;"></i>
            <div>
                <ul class="mb-0" style="padding-left: 0; list-style: none;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Fermer" style="font-size: 10px; opacity: 0.5;"></button>
        </div>
    </div>
@endif