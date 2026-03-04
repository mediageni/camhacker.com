<?php

function paginate($total_pages, $current_page, $base_url) {
    $max_links = 5; // Maximum number of page links to show

    if ($total_pages > 1) {
        echo '<nav aria-label="Pagination">';
        echo '<ul class="pagination justify-content-center">';

        // Previous button
        if ($current_page > 1) {
            echo '<li class="page-item">';
            echo '<a class="page-link" href="' . $base_url . 'page=' . ($current_page - 1) . '" aria-label="Previous">';
            echo '<span aria-hidden="true">&laquo; Previous</span>';
            echo '</a>';
            echo '</li>';
        } else {
            echo '<li class="page-item disabled">';
            echo '<span class="page-link">&laquo; Previous</span>';
            echo '</li>';
        }

        // Determine which pages to show
        $start = max(1, $current_page - floor($max_links / 2));
        $end = min($total_pages, $current_page + floor($max_links / 2));

        if ($start > 1) {
            echo '<li class="page-item"><a class="page-link" href="' . $base_url . 'page=1">1</a></li>';
            if ($start > 2) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Display the page links
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $current_page) {
                echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                echo '<li class="page-item"><a class="page-link" href="' . $base_url . 'page=' . $i . '">' . $i . '</a></li>';
            }
        }

        if ($end < $total_pages) {
            if ($end < $total_pages - 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item"><a class="page-link" href="' . $base_url . 'page=' . $total_pages . '">' . $total_pages . '</a></li>';
        }

        // Next button
        if ($current_page < $total_pages) {
            echo '<li class="page-item">';
            echo '<a class="page-link" href="' . $base_url . 'page=' . ($current_page + 1) . '" aria-label="Next">';
            echo '<span aria-hidden="true">Next &raquo;</span>';
            echo '</a>';
            echo '</li>';
        } else {
            echo '<li class="page-item disabled">';
            echo '<span class="page-link">Next &raquo;</span>';
            echo '</li>';
        }

        echo '</ul>';
        echo '</nav>';
    }
}
