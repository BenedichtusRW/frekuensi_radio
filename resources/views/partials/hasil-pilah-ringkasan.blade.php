<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100 {{ $activeType === 'nelayan' ? 'border border-primary' : '' }}">
            <div class="card-body">
                <div class="small text-muted">HF Nelayan</div>
                <div class="fs-4 fw-bold">{{ $counts['nelayan'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100 {{ $activeType === 'rutin' ? 'border border-primary' : '' }}">
            <div class="card-body">
                <div class="small text-muted">HF Rutin</div>
                <div class="fs-4 fw-bold">{{ $counts['rutin'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100 {{ $activeType === 'mf' ? 'border border-primary' : '' }}">
            <div class="card-body">
                <div class="small text-muted">MF</div>
                <div class="fs-4 fw-bold">{{ $counts['mf'] }}</div>
            </div>
        </div>
    </div>
</div>
