@props([
	'title',
])

<style>
	.help-manual-card {
		background: #fff;
		margin: 40px auto;
		max-width: 1100px;
		width: 100%;
		padding: 60px 80px;
		box-sizing: border-box;
		box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
	}

	.help-manual-header {
		margin-bottom: 24px;
		text-align: center;
	}

	.help-manual-title {
		font-size: 1.6rem;
		font-weight: 700;
		margin: 0 0 6px 0;
		color: #036635;
	}

	.help-footer-logo {
		display: flex;
		justify-content: flex-end;
		margin-top: 4rem;
	}

	.help-footer-logo img {
		height: 32px;
		width: auto;
	}

	.help-manual-body {
		display: grid;
		gap: 16px;
	}

	.help-logo {
		display: flex;
		justify-content: center;
		margin-bottom: 24px;
	}

	.help-logo img {
		height: 64px;
		width: auto;
	}

	.help-section {
		display: grid;
		gap: 10px;
		margin-top: 16px;
	}

	.help-section-title {
		font-size: 1.1rem;
		font-weight: 700;
		color: #036635;
		margin: 0;
	}

    .help-list {
        margin: 0;
        padding-left: 18px;
        color: #444;
        line-height: 1.7;
        list-style-type: disc;
    }

	.help-figure {
		margin: 8px 0 0 0;
	}

	.help-image {
		width: 100%;
		height: auto;
		border: 1px solid #e2e8f0;
		box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
	}

	.help-caption {
		color: #555;
		font-size: 0.95rem;
		margin-top: 10px;
	}

	@media (max-width: 768px) {
		.help-manual-card {
			padding: 40px 40px;
			margin: 20px auto;
		}

		.help-manual-title {
			font-size: 1.45rem;
		}
	}
</style>

<div class="help-manual-card">
	<div class="help-logo">
		<img src="{{ asset('sys-logo.png') }}" alt="Paulinian Student Government logo">
	</div>
	<div class="help-manual-header">
		<h1 class="help-manual-title">{{ $title }}</h1>
	</div>

	<div class="help-manual-body">
		{{ $slot }}
	</div>
	<div class="help-footer-logo">
		<img src="{{ asset('sys-footer.png') }}" alt="System footer logo">
	</div>
</div>
