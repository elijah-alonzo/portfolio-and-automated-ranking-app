<x-filament-panels::page>
	@component('Help.HelpLayout', [
		'title' => 'Certificates',
	])
		<p>
			Certificate help content goes here.
		</p>

		<div class="help-section">
			<h2 class="help-section-title">Back to Overview</h2>
			<ul class="help-list">
				<li><a href="{{ \App\Filament\Resources\Helps\Pages\OverviewHelp::getUrl() }}">Introduction</a></li>
			</ul>
		</div>
	@endcomponent
</x-filament-panels::page>
