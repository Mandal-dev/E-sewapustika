<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;

    }



    /* Hover effect */
    .stat-card:hover {
        background: linear-gradient(135deg, #B789E, #B789E);
        color: #fff;
        transform: translateY(-5px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
    }

    .stat-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        font-size: 1rem;
    }

    .stat-card-icon i {
        font-size: 1.4rem;
        color: #457b9d;
        transition: color 0.3s ease;
    }

    .stat-card:hover .stat-card-icon i {
        color: #fff;
    }

    .stat-card-body {
        display: flex;
        gap: 20px;
        align-items: center;
        flex: 1;
    }

    .chart-container {
        width: 90px;
        height: 90px;
    }

    /* Two-column layout for values */
    .stat-values {
        flex: 1;
        display: grid;
        grid-template-columns: 0.5fr 0.5fr;
        gap: 5px 5px;
        font-size: 0.9rem;
    }

    .stat-values div {
        display: flex;
        justify-content: space-between;
    }

    .stat-card:hover .text-success,
    .stat-card:hover .text-danger,
    .stat-card:hover .text-warning {
        color: #fff !important;
    }
</style>
<div class="dashboard-content">
    <div class="stats-grid">
        @foreach ($cards as $index => $card)
            @php
                $cardRoute = '';
                switch ($card['title']) {
                    case 'Salary Increment':
                        $cardRoute = route('salary_increment.index');
                        break;
                    case 'Rewards':
                        $cardRoute = route('rewards.index');
                        break;
                    case 'Punishments':
                        $cardRoute = route('punishments.index');
                        break;
                    case 'Sewa Pustika':
                        $cardRoute = route('sewa_pustika.index');
                        break;
                }
            @endphp

            <div
                class="stat-card"
                @if($cardRoute)
                    onclick="window.location='{{ $cardRoute }}'"
                    style="cursor: pointer;"
                @endif
            >
                <!-- Header -->
                <div class="stat-card-header">
                    <div class="stat-card-title">{{ __('messages.' . strtolower(str_replace(' ', '_', $card['title']))) }}</div>
                    <div class="stat-card-icon">
                        @if ($card['title'] == 'Salary Increment')
                            <i class="fas fa-upload"></i>
                        @elseif($card['title'] == 'Rewards')
                            <i class="fas fa-gift"></i>
                        @elseif($card['title'] == 'Punishments')
                            <i class="fas fa-gavel"></i>
                        @elseif($card['title'] == 'Sewa Pustika')
                            <i class="fas fa-book"></i>
                        @endif
                    </div>
                </div>
                <!-- Body with chart + stats -->
                <div class="stat-card-body">
                    <div class="stat-values"><br>
                        <div><span></span></div>
                        <div><span></span></div><br>
                        <div><span>{{ __('messages.uploaded') }}:</span><span>{{ $card['total_uploaded'] ?? 0 }}</span></div>

                        <div><span>{{ __('messages.approved') }}:</span><span class="text-success">{{ $card['approved'] ?? 0 }}</span></div>
                        <div><span>{{ __('messages.rejected') }}:</span><span class="text-danger">{{ $card['rejected'] ?? 0 }}</span></div>
                        <div><span>{{ __('messages.pending') }}:</span><span class="text-warning">{{ $card['pending'] ?? 0 }}</span></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>


<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @foreach ($cards as $index => $card)
            new Chart(document.getElementById("chart{{ $index }}"), {
                type: 'doughnut',
                data: {
                    labels: ['Approved', 'Rejected', 'Pending'],
                    datasets: [{
                        data: [
                            {{ $card['approved'] ?? 0 }},
                            {{ $card['rejected'] ?? 0 }},
                            {{ $card['pending'] ?? 0 }}
                        ],
                        backgroundColor: ['#27496d', '#e74c3c', '#f1c40f'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        @endforeach
    });
</script>
