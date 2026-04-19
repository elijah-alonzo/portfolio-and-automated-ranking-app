<x-filament-panels::page>
    @component ('Help.HelpLayout', [
        'title' => 'Certifictates',
    ])
        <p>
            <strong>Certificates</strong> are used to recognize the achievements
            and contributions of students within the council system. The issued
            certificates can be downloaded by student officers and is
            automatically reflected on their portfolio.
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/certificates/certificates-table.png') }}"
                    alt="Certificate table"
                />
                <figcaption class="help-caption">
                    Table displaying all certificates issued to each student.
                </figcaption>
            </figure>
        <div class="help-section">
            <h2 class="help-section-title">Issuing a Certificate</h2>
            <p>Certificates can be issued to students by <strong>Admins</strong> and <strong>Advisers</strong>. When issuing a certificate, you must select the students, upload the certificate file, and set the date of issuance.</p>
            <figure class="help-figure">
                <img
                    class="help-image"
                    src="{{ asset('assets/certificates/certificates-form.png') }}"
                    alt="Certificate form"
                />
                <figcaption class="help-caption">
                    Form for issuing certificates.
                </figcaption>
            </figure>
        </div>

    @endcomponent
</x-filament-panels::page>
