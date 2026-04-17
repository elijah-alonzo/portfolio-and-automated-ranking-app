<x-filament-panels::page>
    @include('EvaluationForm.EvaluationFormLayout')

    <div class="ef-evaluation-card">
        <div class="ef-evaluation-header">
            <h1 class="ef-evaluation-title">{{ $this->getTitle() }}</h1>
            <p class="ef-evaluation-subheading">{{ $this->getSubheading() }}</p>
        </div>
        <div class="ef-evaluation-content">
            @if($isLocked && $existingForm && $existingForm->status === 'submitted')
                <div class="ef-locked-message">
                    <strong>Evaluation Completed:</strong> This evaluation has already been submitted and cannot be edited.
                </div>
            @elseif($isLocked)
                <div class="ef-locked-message">
                    <strong>Read-only View:</strong> This evaluation is locked for editing in your role.
                </div>
            @endif
            @if(!$isLocked && $existingForm && $existingForm->status === 'draft')
                <div class="ef-locked-message">
                    <strong>Draft Saved:</strong> You are editing a saved draft. Submit when ready.
                </div>
            @endif
            <form method="POST" id="evaluation-form" action="{{ route('evaluation.submit', [$evaluation->id, $evaluatee->id, $evaluationType]) }}">
                @csrf
                @php
                    $rubric = \App\Models\EvaluationForm::getRubricStructure();
                    $questionsByDomainStrand = [];
                    foreach ($questions as $qKey => $q) {
                        $questionsByDomainStrand[$q['domain_key']][$q['strand_key']][$qKey] = $q;
                    }
                @endphp
                @foreach($rubric as $domainKey => $domain)
                    @if(isset($questionsByDomainStrand[$domainKey]))
                        <div class="ef-domain-section">
                            <div class="ef-domain-header">
                                <div class="ef-domain-title">Domain {{ substr($domainKey, -1) }}: {{ $domain['title'] }}</div>
                                @if($domain['description'])
                                    <div class="ef-domain-description">{{ $domain['description'] }}</div>
                                @endif
                            </div>
                            @foreach($domain['strands'] as $strandKey => $strand)
                                @if(isset($questionsByDomainStrand[$domainKey][$strandKey]))
                                    <div class="ef-strand">
                                        <div class="ef-strand-title">Strand {{ substr($strandKey, -1) }}. {{ $strand['title'] }}</div>
                                        <div class="ef-questions-container">
                                            @foreach($questionsByDomainStrand[$domainKey][$strandKey] as $questionKey => $question)
                                                @if($questionKey === \App\Models\EvaluationForm::LENGTH_OF_SERVICE_KEY)
                                                    <div class="ef-question-item">
                                                        <div class="ef-question-text">{{ $question['text'] }}</div>
                                                        <div class="ef-length-service-note">
                                                            <strong>{{ $evaluatee->name ?? 'This student' }}</strong> has served
                                                            <strong>{{ $lengthOfServiceYears ?? 0 }}</strong> year(s) in
                                                            <strong>{{ $evaluation->council->name ?? 'this council' }}</strong>.
                                                        </div>
                                                        <input type="hidden"
                                                               name="answers[{{ $questionKey }}]"
                                                               value="{{ $lengthOfServiceScore ?? 0 }}">
                                                    </div>
                                                @else
                                                    <div class="ef-question-item">
                                                        <div class="ef-question-text">{{ $question['text'] }}</div>
                                                        <div class="ef-rating-scale">
                                                            @foreach($question['criteria'] as $value => $criteria)
                                                                <label class="ef-rating-option {{ isset($data[$questionKey]) && $data[$questionKey] == $value ? 'selected' : '' }}">
                                                                    <input type="radio"
                                                                           name="answers[{{ $questionKey }}]"
                                                                           value="{{ $value }}"
                                                                           {{ isset($data[$questionKey]) && $data[$questionKey] == $value ? 'checked' : '' }}
                                                                           {{ $isLocked ? 'disabled' : '' }}>
                                                                    <span class="ef-rating-label">
                                                                        <span class="ef-rating-value">{{ $value }}</span>
                                                                        <span class="ef-rating-criteria">{{ $criteria }}</span>
                                                                    </span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </form>
        </div>
        <div class="ef-form-actions">
            @if(!$isLocked)
                <button type="submit" form="evaluation-form" name="submission_action" value="draft" class="ef-btn ef-btn-info">
                    Save Draft
                </button>
                <button type="submit" form="evaluation-form" name="submission_action" value="submitted" class="ef-btn ef-btn-primary" onclick="return confirm('Are you sure you want to submit this evaluation? You will not be able to edit it afterwards.');">
                    Submit Evaluation
                </button>
            @endif
        </div>
    </div>
</x-filament-panels::page>