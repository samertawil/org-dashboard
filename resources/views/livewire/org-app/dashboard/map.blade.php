<div>
    
    <div class="progress-bar">
        <div class="progress"
            style="width: {{ (count(array_filter($this->steps, fn($s) => $s['completed'])) / count($this->steps)) * 100 }}%">
        </div>
    </div>
    <p>تقدم الإعداد: {{ count(array_filter($this->steps, fn($s) => $s['completed'])) }} / {{ count($this->steps) }}</p>
    <div class="dashboard-map">
        <h2>مسار إعداد الفصل الدراسي</h2>

        <div class="steps-timeline">
            @foreach ($this->steps as $key => $step)
                <div class="step-item {{ $step['completed'] ? 'completed' : 'pending' }}">
                    <div class="step-icon">
                        {{ $step['icon'] }}
                        <span class="step-number">{{ array_search($key, array_keys($this->steps)) + 1 }}</span>
                    </div>

                    <div class="step-content">
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['completed'] ? 'تم' : 'قيد الإعداد' }}</p>
                        {{-- <a href="{{ route($step['route']) }}" class="btn"> --}}
                        {{ $step['completed'] ? 'عرض' : 'ابدأ الآن' }}
                        </a>
                    </div>

                    @if ($step['completed'])
                        <div class="checkmark">✔️</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <style>
        .dashboard-map {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .steps-timeline {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .step-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-radius: 12px;
            background: #f8f9fa;
            border-left: 5px solid #007bff;
            transition: all 0.3s;
        }

        .step-item.completed {
            background: #d4edda;
            border-left-color: #28a745;
        }

        .step-icon {
            font-size: 3rem;
            margin-right: 20px;
            position: relative;
        }

        .step-number {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #007bff;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .checkmark {
            margin-left: auto;
            font-size: 2rem;
        }

        @media (max-width: 768px) {
            .step-item {
                flex-direction: column;
                text-align: center;
            }

            .step-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
</div>
