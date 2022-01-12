
<div>
    @if (auth()->check())
        <script>
            var bstc_livewire_started = false;
            var bstc_hubspot_started = false;

            window.hsConversationsSettings = {
                loadImmediately: false,
            };

            function startConversations() {
                if (!bstc_livewire_started || !bstc_hubspot_started) {
                    return;
                }

                @this.getIdentificationToken().then((response) => {
                    window.hsConversationsSettings.identificationEmail = response.identification_email;
                    window.hsConversationsSettings.identificationToken = response.token;
                    window.HubSpotConversations.widget.load();
                });
            }

            function onConversationsAPIReady() {
                bstc_hubspot_started = true;
                startConversations();
            }

            if (window.HubSpotConversations) {
                onConversationsAPIReady();
            } else {
                window.hsConversationsOnReady = [onConversationsAPIReady];
            }

            document.addEventListener('livewire:load', function () {
                bstc_livewire_started = true;
                startConversations();
            });
        </script>
    @endif

    <script type="text/javascript" id="hs-script-loader" async defer src="{{ config('hubspot.script') }}"></script>
</div>
