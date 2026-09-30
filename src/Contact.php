<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Exception\GuzzleException;

/**
 * The project address book.
 */
class Contact extends Service
{
    /**
     * The project contacts with their labels, a page at a time.
     * The result carries the pagination `links` and `meta` next to the data.
     *
     * @param int $perPage 1 - 100
     */
    public function list(int $page = 1, int $perPage = 15): array
    {
        try {
            $response = $this->client->get('contacts', [
                'query' => ['page' => $page, 'per_page' => $perPage],
                'timeout' => 10,
            ]);
        } catch (GuzzleException $e) {
            return $this->failed($e);
        }
        return $this->success($response, ['links', 'meta']);
    }

    /**
     * Add a contact, a phone number can only be used once in the project.
     *
     * @param array{phone?: string, name?: string|null, labels?: list<string>} $options e.g. ['phone' => '0700100100', 'name' => 'Jane Wanjiku', 'labels' => ['LABEL ID']]
     */
    public function create(array $options = []): array
    {
        if (empty($options['phone'])) {
            return $this->error(7, 'phone must be defined.');
        }

        try {
            $response = $this->client->post('contacts', [
                'json' => $this->fields($options),
                'timeout' => 10,
            ]);
        } catch (GuzzleException $e) {
            return $this->failed($e);
        }
        return $this->success($response);
    }

    /**
     * A contact of the project.
     */
    public function get(string $contactId): array
    {
        if ($contactId === '') {
            return $this->error(7, 'contact id must be defined.');
        }

        try {
            $response = $this->client->get('contacts/' . rawurlencode($contactId), ['timeout' => 10]);
        } catch (GuzzleException $e) {
            return $this->failed($e);
        }
        return $this->success($response);
    }

    /**
     * Update the fields given, an omitted field is left as it is. `labels` replaces the contact labels, `[]` removes them all.
     *
     * @param array{phone?: string, name?: string|null, labels?: list<string>} $options
     */
    public function update(string $contactId, array $options = []): array
    {
        if ($contactId === '') {
            return $this->error(7, 'contact id must be defined.');
        }

        try {
            $response = $this->client->put('contacts/' . rawurlencode($contactId), [
                'json' => (object) $this->fields($options),
                'timeout' => 10,
            ]);
        } catch (GuzzleException $e) {
            return $this->failed($e);
        }
        return $this->success($response);
    }

    /**
     * Remove the contact, its labels stay.
     */
    public function delete(string $contactId): array
    {
        if ($contactId === '') {
            return $this->error(7, 'contact id must be defined.');
        }

        try {
            $response = $this->client->delete('contacts/' . rawurlencode($contactId), ['timeout' => 10]);
        } catch (GuzzleException $e) {
            return $this->failed($e);
        }
        return $this->success($response);
    }

    protected function fields(array $options): array
    {
        return array_intersect_key($options, array_flip(['phone', 'name', 'labels']));
    }
}
