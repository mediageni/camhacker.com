<?php
// Ensure the filters and required variables are available to the form
if (!isset($filters)) {
    $filters = [
        'search' => '',
        'country' => '',
        'city' => '',
        'manufacturer' => ''
    ];
}
?>
<!-- Search Form Content -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <!-- Free-text search query -->
        <input type="text" class="form-control" name="search" placeholder="Enter title, country, or manufacturer" value="<?php echo htmlspecialchars($filters['search']); ?>">
    </div>
    <div class="col-12">
        <!-- Country Dropdown -->
        <select name="country" id="country" class="form-select">
            <option value="">Country</option>
            <?php foreach ($countries as $country_value): ?>
                <option value="<?php echo htmlspecialchars($country_value); ?>" <?php if ($filters['country'] === $country_value) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($country_value); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-12">
        <!-- City Dropdown -->
        <select name="city" id="city" class="form-select">
            <option value="">City</option>
            <?php if ($filters['country']): ?>
                <?php 
                $cities = getCitiesByCountry($conn, $filters['country']); 
                foreach ($cities as $city_value): ?>
                    <option value="<?php echo htmlspecialchars($city_value); ?>" <?php if ($filters['city'] === $city_value) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($city_value); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <div class="col-12">
        <!-- Manufacturer Dropdown -->
        <select name="manufacturer" id="manufacturer" class="form-select">
            <option value="">Manufacturer</option>
            <?php foreach ($manufacturers as $manufacturer_value): ?>
                <option value="<?php echo htmlspecialchars($manufacturer_value); ?>" <?php if ($filters['manufacturer'] === $manufacturer_value) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($manufacturer_value); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <!-- No submit button here -->
</div>
