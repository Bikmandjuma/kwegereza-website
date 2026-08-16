@extends('Users.admin.cover')
@section('content')
<!-- Content header -->
<!-- Main content -->
            <div class="flex items-center justify-between px-4 py-4 border-b lg:py-6 dark:border-primary-darker">
              <h1 class="text-2xl font-semibold text-primary-dark dark:text-light" style="font-family: Times New Roman">{{ Auth()->guard('owner')->user()->title }}'s account</h1>
              <a
                href="#"
                class="px-4 py-2 text-sm text-white rounded-md bg-primary hover:bg-primary-dark focus:outline-none focus:ring focus:ring-primary focus:ring-offset-1 focus:ring-offset-white dark:focus:ring-offset-dark"
              >
                Dashboard
              </a>
            </div>

            <!-- Content -->
            <div class="mt-2">
              <!-- Stat cards — real platform metrics -->
              <div class="grid grid-cols-1 gap-8 p-4 lg:grid-cols-2 xl:grid-cols-4">

                <!-- Card 1: All System Users / Visits Today -->
                <div class="flex items-center justify-between p-4 bg-white rounded-md dark:bg-darker">
                  <div>
                    <h6 class="text-xs font-medium leading-none tracking-wider text-gray-500 uppercase dark:text-primary-light">
                      Abakoresha Bose (All Users)
                    </h6>
                    <span class="text-xl font-semibold" id="allSystemUsersCount">{{ $allSystemUsersCount }}</span>
                    <br>
                    <hr class="p-2">
                    <h6 class="text-xs font-medium leading-none tracking-wider text-gray-500 uppercase dark:text-primary-light mt-1">
                      Abasuye Uyu Munsi <span class="inline-block px-2 py-px ml-2 text-xs text-green-500 bg-green-100 rounded-md" id="todaysVisitCount">{{ $todaysVisitCount }}</span>
                    </h6>
                  </div>
                  <div>
                    <span>
                      <svg class="w-12 h-12 text-gray-300 dark:text-primary-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                      </svg>
                    </span>
                  </div>
                </div>

                <!-- Card 2: Teachers / Amatangazo -->
                <div class="flex items-center justify-between p-4 bg-white rounded-md dark:bg-darker">
                  <div>
                    <h6 class="text-xs font-medium leading-none tracking-wider text-gray-500 uppercase dark:text-primary-light">
                      Abarimu (Sheikh, Ustaz)
                    </h6>
                    <span class="text-xl font-semibold" id="teachersCount">{{ $teachersCount }}</span>
                    <br>
                    <hr class="p-2">
                    <h6 class="text-xs font-medium leading-none tracking-wider text-gray-500 uppercase dark:text-primary-light mt-1">
                      Amatangazo <span class="inline-block px-2 py-px ml-2 text-xs text-green-500 bg-green-100 rounded-md" id="amatangazoCount">{{ $amatangazoCount }}</span>
                    </h6>
                  </div>
                  <div>
                    <span>
                      <svg class="w-12 h-12 text-gray-300 dark:text-primary-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                      </svg>
                    </span>
                  </div>
                </div>

                <!-- Card 3: All Darsat / Online Users -->
                <div class="flex items-center justify-between p-4 bg-white rounded-md dark:bg-darker">
                  <div>
                    <h6 class="text-xs font-medium leading-none tracking-wider text-gray-500 uppercase dark:text-primary-light">
                      Darsat Zose
                    </h6>
                    <span class="text-xl font-semibold" id="allDarsatCount">{{ $allDarsatCount }}</span>
                    <br>
                    <hr class="p-2">
                    <h6 class="text-xs font-medium leading-none tracking-wider text-gray-500 uppercase dark:text-primary-light mt-1">
                      Abari Kuri Line (Online) <span class="inline-block px-2 py-px ml-2 text-xs text-green-500 bg-green-100 rounded-md" id="onlineUsersCount">{{ $onlineUsersCount }}</span>
                    </h6>
                  </div>
                  <div>
                    <span>
                      <svg class="w-12 h-12 text-gray-300 dark:text-primary-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                      </svg>
                    </span>
                  </div>
                </div>

                <!-- Card 4: Books / Online Guests -->
                <div class="flex items-center justify-between p-4 bg-white rounded-md dark:bg-darker">
                  <div>
                    <h6 class="text-xs font-medium leading-none tracking-wider text-gray-500 uppercase dark:text-primary-light">
                      Ibitabo (Books)
                    </h6>
                    <span class="text-xl font-semibold" id="booksCount">{{ $booksCount }}</span>
                    <br>
                    <hr class="p-2">
                    <h6 class="text-xs font-medium leading-none tracking-wider text-gray-500 uppercase dark:text-primary-light mt-1">
                      Abasuye Batinjiye (Online Guests) <span class="inline-block px-2 py-px ml-2 text-xs text-green-500 bg-green-100 rounded-md" id="onlineGuestCount">{{ $onlineGuestCount }}</span>
                    </h6>
                  </div>
                  <div>
                    <span>
                      <svg class="w-12 h-12 text-gray-300 dark:text-primary-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                      </svg>
                    </span>
                  </div>
                </div>

              </div>

              <script>
                // Lightweight periodic refresh of just the 8 numbers above —
                // no full page reload, no fake data. Every 15s, not every 1s
                // like the old version (that was needlessly hammering the
                // server for numbers that rarely change second-to-second).
                function kiuRefreshDashboardCounts() {
                    fetch('{{ route("owner.refresh_counts") }}')
                        .then(r => r.json())
                        .then(data => {
                            document.getElementById('allSystemUsersCount').innerText = data.allSystemUsersCount;
                            document.getElementById('teachersCount').innerText = data.teachersCount;
                            document.getElementById('allDarsatCount').innerText = data.allDarsatCount;
                            document.getElementById('booksCount').innerText = data.booksCount;
                            document.getElementById('todaysVisitCount').innerText = data.todaysVisitCount;
                            document.getElementById('amatangazoCount').innerText = data.amatangazoCount;
                            document.getElementById('onlineUsersCount').innerText = data.onlineUsersCount;
                            document.getElementById('onlineGuestCount').innerText = data.onlineGuestCount;
                            if (typeof kiuUpdateHeaderOnlineCount === 'function') {
                                kiuUpdateHeaderOnlineCount(data.onlineUsersCount);
                            }
                        })
                        .catch(err => console.error('Dashboard refresh failed:', err));
                }
                setInterval(kiuRefreshDashboardCounts, 15000);
              </script>

              <!-- 4 independent real charts, each with its own day/week/month/year selector -->
              <div class="grid grid-cols-1 gap-8 p-4 md:grid-cols-2">

                @foreach([
                    ['metric' => 'students', 'title' => 'Abanyeshuri Biyandikishije (Students Joined)', 'color' => '#058e48'],
                    ['metric' => 'darsat', 'title' => 'Darsat Zongewemo (Darsat Added)', 'color' => '#e2b45f'],
                    ['metric' => 'amatangazo', 'title' => 'Amatangazo Ashyizweho', 'color' => '#094939'],
                    ['metric' => 'online_users', 'title' => "Abari Kuri Line ku Munsi (Online Users)", 'color' => '#c8a36c'],
                ] as $chart)
                <div class="bg-white rounded-md dark:bg-darker kiu-chart-card" data-metric="{{ $chart['metric'] }}" data-color="{{ $chart['color'] }}">
                  <div class="flex items-center justify-between p-4 border-b dark:border-primary">
                    <h4 class="text-lg font-semibold text-gray-500 dark:text-light">{{ $chart['title'] }}</h4>
                    <select class="px-3 py-1 text-xs border rounded-lg kiu-chart-period dark:bg-dark dark:text-light" data-metric="{{ $chart['metric'] }}">
                      <option value="day" selected>Buri Munsi</option>
                      <option value="week">Buri Cyumweru</option>
                      <option value="month">Buri Kwezi</option>
                      <option value="year">Buri Mwaka</option>
                    </select>
                  </div>
                  <div class="relative p-4 h-64">
                    <canvas id="kiuChart-{{ $chart['metric'] }}"></canvas>
                  </div>
                </div>
                @endforeach

              </div>
            </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const chartInstances = {};

    function loadChart(metric, period) {
        const card = document.querySelector(`.kiu-chart-card[data-metric="${metric}"]`);
        const color = card.dataset.color;

        fetch(`{{ route('owner.dashboardChartData') }}?metric=${metric}&period=${period}`)
            .then(r => r.json())
            .then(data => {
                const canvas = document.getElementById('kiuChart-' + metric);
                if (chartInstances[metric]) {
                    chartInstances[metric].data.labels = data.labels;
                    chartInstances[metric].data.datasets[0].data = data.data;
                    chartInstances[metric].update();
                    return;
                }
                chartInstances[metric] = new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            borderColor: color,
                            backgroundColor: color + '33',
                            fill: true,
                            tension: 0.3,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                    }
                });
            })
            .catch(err => console.error('Chart load failed for ' + metric, err));
    }

    document.querySelectorAll('.kiu-chart-period').forEach(select => {
        select.addEventListener('change', function() {
            loadChart(this.dataset.metric, this.value);
        });
        loadChart(this.dataset.metric, 'day');
    });
})();
</script>

@endsection
