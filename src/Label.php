<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp;

use GuzzleHttp\Exception\GuzzleException;

/**
 * Groups of contacts, a label can be sent to in one request.
 */
class Label extends Service
{
    /**
     * The project labels with their contact counts, a page at a time.
     * The result carries the pagination `links` and `meta` next to the data.
     *
     * @param int $perPage 1 - 100
     */
    public function list(int $page = 1, int $perPage = 15): array
    {
        try {
            $response = $this->client->get('labels', [
                'query' => ['page' => $page, 'per_page' => $perPage],
                'timeout' => 10,
            ]);
        } catch (GuzzleException $e) {
            return $this->failed($e);
        }
        return $this->success($response, ['links', 'meta']);
    }

    /**
     * Add a label, names are unique in the project (max 50 characters).
     */
    public function create(string $name): array
    {
        if (trim($name) === '') {
            return $this->error(7, 'name must be defined.');
        }

        try {
            $response = $this->client->post('labels', [
                'json' => ['name' => $name],
                'timeout' => 10,
            ]);
        } catch (GuzzleException $e) {
            return $this->failed($e);
        }
        return $this->success($response);
    }

    /**
     * A label of the project.
     */
    public function get(string $labelId): array
    {
        if ($labelId === '') {
            return $this->error(7, 'label id must be defined.');
        }

        try {
            $response = $this->client->get('labels/' . rawurlencode($labelId), ['timeout' => 10]);
        } catch (GuzzleException $e) {
            return $this->failed($e);
        }
        return $this->success($response);
    }

    /**
     * Rename the label.
     */
    public function update(string $labelId, string $name): array
    {
        if ($labelId === '' || trim($name) === '') {
            return $this->error(7, 'label id and name must be defined.');
        }

        try {
            $response = $this->client->put('labels/' . rawurlencode($labelId), [
                'json' => ['name' => $name],
                'timeout' => 10,
            ]);
        } catch (GuzzleException $e) {
            return $this->failed($e);
        }
        return $this->success($response);
    }

    /**
     * Rename the label.
     */
    public function rename(string $labelId, string $name): array
    {
        return $this->update($labelId, $name);
    }

    /**
     * Remove the label, its contacts stay.
     */
    public function delete(string $labelId): array
    {
        if ($labelId === '') {
            return $this->error(7, 'label id must be defined.');
        }

        try {
            $response = $this->client->delete('labels/' . rawurlencode($labelId), ['timeout' => 10]);
        } catch (GuzzleException $e) {
            return $this->failed($e);
        }
        return $this->success($response);
    }
}
