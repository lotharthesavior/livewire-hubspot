<?php

namespace LivewireHubspot;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class HubspotTrackingCode extends Component
{
    /** @var User */
    public $user = null;

    /** @var array */
    public $fields;

    public function mount(array $fields = [])
    {
        if (auth()->check()) {
            $this->user = auth()->user();
        }

        $this->fields = $fields;
    }

    public function getIdentificationToken()
    {
        $response = Http::post('https://api.hubspot.com/conversations/v3/visitor-identification/tokens/create?hapikey=' . config('hubspot.api-token'), [
            'email' => $this->user->email ?? null,
        ]);

        $response_body = $response->json();

        if ($response->status() !== 200) {
            return null;
        }

        $this->updateCustomerInfo();

        return array_merge($response_body, [
            'identification_email' => $this->user->email ?? null,
        ]);
    }

    private function updateCustomerInfo()
    {
        if ($this->user === null || empty($this->fields)) {
            return;
        }

        $contact = $this->getContact();

        if ($contact === null || !$this->needsUpdate($contact)) {
            return;
        }

        $response = Http::post('https://api.hubapi.com/contacts/v1/contact/vid/' . $contact['vid'] . '/profile?hapikey=' . config('hubspot.api-token'), [
            'properties' => $this->getProperties(),
        ]);

        if ($response->status() !== 204) {
            Log::info('There was a problem to update user information on hubspot during support. User: ' . $this->user->id);
        }
    }

    /**
     * @return array|null
     */
    private function getContact()
    {
        $response = Http::get('https://api.hubapi.com/contacts/v1/contact/email/' . $this->user->email . '/profile?hapikey=' . config('hubspot.api-token'));

        $contact = $response->json();

        if ($response->status() !== 200 || !isset($contact['vid'])) {
            Log::info('There was a problem to retrieve customer from on hubspot. User: ' . $this->user->id);
            return null;
        }

        return $contact;
    }

    private function getProperties(): array
    {
        $properties = array();

        foreach ($this->fields as $key => $value) {
            if ($value === null) {
                continue;
            }

            $properties[] = [
                'property' => $key,
                'value' => $value,
            ];
        }

        return $properties;
    }

    private function needsUpdate(array $contact): bool
    {
        foreach ($this->fields as $key => $value) {
            if (!isset($contact['properties'][$key]) || $contact['properties'][$key]['value'] !== $value) {
                return true;
            }
        }

        return false;
    }

    public function render()
    {
        return view('livewire-hubspot::hubspot-tracking-code');
    }
}
