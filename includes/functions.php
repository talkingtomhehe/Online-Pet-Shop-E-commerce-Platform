<?php
// filepath: c:\xampp\htdocs\chabongshop\includes\functions.php
// Helper functions

/**
 * Generate URL for sorting products
 */
// Update the getSortURL function to prevent double encoding:

function getSortURL($sortField, $currentSort, $currentOrder) {
    global $controller, $action, $id, $search, $page;
    
    // Determine new order direction
    $newOrder = ($sortField == $currentSort && $currentOrder == 'asc') ? 'desc' : 'asc';
    
    $url = SITE_URL;
    
    // Build URL based on current controller and action
    if ($controller === 'products') {
        $url .= 'products';
        
        if ($action === 'category' && $id) {
            // First decode $id to make sure we're not double-encoding
            $decodedId = urldecode($id);
            $url .= '/category/' . urlencode($decodedId);
        } elseif ($action === 'search' && !empty($search)) {
            $url .= '/search';
        }
    }
    
    // Start with question mark for query parameters
    $url .= '?sort=' . urlencode($sortField) . '&order=' . $newOrder;
    
    // Add remaining parameters
    if (!empty($search)) {
        $url .= "&search=" . urlencode($search);
    }
    
    if (isset($page) && $page > 1) {
        $url .= "&page=" . $page;
    }
    
    return $url;
}

/**
 * Generate HTML for sort icon
 */
function getSortIcon($sortField, $currentSort, $currentOrder) {
    if ($sortField != $currentSort) {
        return '<i class="bi bi-arrow-down-up text-muted"></i>';
    } else {
        return ($currentOrder == 'asc') 
            ? '<i class="bi bi-sort-alpha-down"></i>' 
            : '<i class="bi bi-sort-alpha-up-alt"></i>';
    }
}

/**
 * Generate URL for pagination links
 */
// Update the getPaginationURL function:

function getPaginationURL($pageNum) {
    global $controller, $action, $id, $search, $sort, $order;
    
    $url = SITE_URL;
    
    // Build URL based on current controller and action
    if ($controller === 'products') {
        $url .= 'products';
        
        if ($action === 'category' && $id) {
            // First decode $id to make sure we're not double-encoding
            $decodedId = urldecode($id);
            $url .= '/category/' . urlencode($decodedId);
        } elseif ($action === 'search' && !empty($search)) {
            $url .= '/search';
        }
    }
    
    // Start with question mark for query parameters
    $url .= '?page=' . $pageNum;
    
    // Add other parameters
    if (!empty($search)) {
        $url .= "&search=" . urlencode($search);
    }
    
    if (!empty($sort)) {
        $url .= "&sort=" . urlencode($sort);
    }
    
    if (!empty($order)) {
        $url .= "&order=" . urlencode($order);
    }
    
    return $url;
}