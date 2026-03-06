<?php
class CamDatabase {
    private $dataFile;
    private $webcams = [];
    private static $instance = null;

    private function __construct() {
        $this->dataFile = DATA_FILE;
        $this->load();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function load() {
        if (file_exists($this->dataFile)) {
            $json = file_get_contents($this->dataFile);
            $this->webcams = json_decode($json, true) ?: [];
        }
    }

    public function save() {
        $json = json_encode($this->webcams, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        file_put_contents($this->dataFile, $json, LOCK_EX);
    }

    public function getAll() {
        return $this->webcams;
    }

    public function getById($id) {
        foreach ($this->webcams as $cam) {
            if ((int)$cam['id'] === (int)$id) {
                return $cam;
            }
        }
        return null;
    }

    public function search($filters = [], $limit = null, $offset = 0) {
        $results = $this->webcams;

        if (!empty($filters['search'])) {
            $term = strtolower($filters['search']);
            $results = array_filter($results, fn($cam) =>
                stripos($cam['title_seo'] ?? '', $term) !== false
                || stripos($cam['country'] ?? '', $term) !== false
                || stripos($cam['city'] ?? '', $term) !== false
                || stripos($cam['state'] ?? '', $term) !== false
                || stripos($cam['manufacturer'] ?? '', $term) !== false
                || stripos($cam['tag'] ?? '', $term) !== false
                || stripos($cam['zipcode'] ?? '', $term) !== false
            );
        }

        if (!empty($filters['country'])) {
            $val = strtolower(str_replace('-', ' ', $filters['country']));
            $filterVal = $filters['country'];
            $results = array_filter($results, fn($c) =>
                strtolower($c['country'] ?? '') === $val
                || $this->slugify($c['country'] ?? '') === $filterVal
            );
        }

        if (!empty($filters['city'])) {
            $val = strtolower(str_replace('-', ' ', $filters['city']));
            $filterVal = $filters['city'];
            $results = array_filter($results, fn($c) =>
                strtolower($c['city'] ?? '') === $val
                || $this->slugify($c['city'] ?? '') === $filterVal
            );
        }

        if (!empty($filters['manufacturer'])) {
            $val = strtolower(str_replace('-', ' ', $filters['manufacturer']));
            $filterVal = $filters['manufacturer'];
            $results = array_filter($results, fn($c) =>
                strtolower($c['manufacturer'] ?? '') === $val
                || $this->slugify($c['manufacturer'] ?? '') === $filterVal
            );
        }

        if (!empty($filters['tag'])) {
            $val = strtolower(str_replace('-', ' ', $filters['tag']));
            $filterVal = $filters['tag'];
            $results = array_filter($results, fn($c) =>
                strtolower($c['tag'] ?? '') === $val
                || $this->slugify($c['tag'] ?? '') === $filterVal
            );
        }

        if (!empty($filters['country_code'])) {
            $val = strtoupper($filters['country_code']);
            $results = array_filter($results, fn($c) =>
                strtoupper($c['country_code'] ?? '') === $val
            );
        }

        $results = array_values($results);
        $total = count($results);

        if ($limit !== null) {
            $results = array_slice($results, $offset, $limit);
        }

        return ['data' => $results, 'total' => $total];
    }

    public function getTopViewed($limit = 3) {
        $sorted = $this->webcams;
        usort($sorted, fn($a, $b) => ($b['view_count'] ?? 0) - ($a['view_count'] ?? 0));
        return array_slice($sorted, 0, $limit);
    }

    public function getRandom($limit = 20) {
        $shuffled = $this->webcams;
        shuffle($shuffled);
        return array_slice($shuffled, 0, $limit);
    }

    public function getDistinct($field) {
        $values = array_unique(array_filter(array_map(fn($c) => $c[$field] ?? '', $this->webcams)));
        sort($values);
        return array_values($values);
    }

    public function getCitiesByCountry($country) {
        $cities = [];
        foreach ($this->webcams as $cam) {
            if (strtolower($cam['country'] ?? '') === strtolower($country) && !empty($cam['city'])) {
                $cities[] = $cam['city'];
            }
        }
        $cities = array_unique($cities);
        sort($cities);
        return array_values($cities);
    }

    public function incrementViews($id) {
        foreach ($this->webcams as &$cam) {
            if ((int)$cam['id'] === (int)$id) {
                $cam['view_count'] = ($cam['view_count'] ?? 0) + 1;
                $this->save();
                return true;
            }
        }
        return false;
    }

    public function delete($id) {
        $this->webcams = array_values(array_filter($this->webcams, fn($c) => (int)$c['id'] !== (int)$id));
        $this->save();
        return true;
    }

    public function update($id, $data) {
        foreach ($this->webcams as &$cam) {
            if ((int)$cam['id'] === (int)$id) {
                foreach ($data as $key => $value) {
                    $cam[$key] = $value;
                }
                $this->save();
                return true;
            }
        }
        return false;
    }

    public function add($data) {
        $maxId = 0;
        foreach ($this->webcams as $cam) {
            if ((int)$cam['id'] > $maxId) $maxId = (int)$cam['id'];
        }
        $data['id'] = $maxId + 1;
        $data['view_count'] = $data['view_count'] ?? 0;
        $this->webcams[] = $data;
        $this->save();
        return $data['id'];
    }

    public function getStats() {
        $countries = $this->getDistinct('country');
        $cities = $this->getDistinct('city');
        $manufacturers = $this->getDistinct('manufacturer');
        $totalViews = array_sum(array_column($this->webcams, 'view_count'));
        return [
            'total_cams' => count($this->webcams),
            'total_countries' => count($countries),
            'total_cities' => count($cities),
            'total_manufacturers' => count($manufacturers),
            'total_views' => $totalViews,
        ];
    }

    public function getCountryCounts() {
        $counts = [];
        foreach ($this->webcams as $cam) {
            $country = $cam['country'] ?? 'Unknown';
            $code = strtolower($cam['country_code'] ?? '');
            if (!isset($counts[$country])) {
                $counts[$country] = ['count' => 0, 'code' => $code];
            }
            $counts[$country]['count']++;
        }
        ksort($counts);
        return $counts;
    }

    public function slugify($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }

    // Keep public alias for backward compatibility
    public function slugifyPublic($text) {
        return $this->slugify($text);
    }

    public function getMapData() {
        $markers = [];
        foreach ($this->webcams as $cam) {
            $lat = (float)($cam['latitude'] ?? 0);
            $lng = (float)($cam['longitude'] ?? 0);
            if ($lat != 0 && $lng != 0 && abs($lat) <= 90 && abs($lng) <= 180) {
                $markers[] = [
                    'id' => $cam['id'],
                    'lat' => $lat,
                    'lng' => $lng,
                    'title' => $cam['title_seo'] ?? '',
                    'city' => $cam['city'] ?? '',
                    'country' => $cam['country'] ?? '',
                    'country_code' => strtolower($cam['country_code'] ?? ''),
                ];
            }
        }
        return $markers;
    }
}
