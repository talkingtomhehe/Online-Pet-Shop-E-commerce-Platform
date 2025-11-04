<?php
// Ensure required variables exist
if (!isset($currentPage) || !isset($totalPages) || !isset($urlFunction) || !is_callable($urlFunction)) {
    return;
}

// Don't show pagination if only one page
if ($totalPages <= 1) {
    return;
}

// Calculate range of page numbers to show
$maxPagesToShow = 5;
$pagesToTheLeft = floor($maxPagesToShow / 2);
$pagesToTheRight = $maxPagesToShow - $pagesToTheLeft - 1;
$startPage = max(1, $currentPage - $pagesToTheLeft);
$endPage = min($totalPages, $currentPage + $pagesToTheRight);

// Adjust start/end if we're at edges to always show maxPagesToShow
if ($endPage - $startPage + 1 < $maxPagesToShow) {
    if ($startPage == 1) {
        $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);
    }
    if ($endPage == $totalPages) {
        $startPage = max(1, $endPage - $maxPagesToShow + 1);
    }
}
?>

<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
        <!-- Previous button -->
        <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $urlFunction($currentPage - 1); ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span> Previous
            </a>
        </li>

        <?php if ($startPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo $urlFunction(1); ?>">1</a>
            </li>
            <?php if ($startPage > 2): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <li class="page-item <?php echo ($currentPage == $i) ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo $urlFunction($i); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($endPage < $totalPages): ?>
            <?php if ($endPage < $totalPages - 1): ?>
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo $urlFunction($totalPages); ?>"><?php echo $totalPages; ?></a>
            </li>
        <?php endif; ?>

        <!-- Next button -->
        <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $urlFunction($currentPage + 1); ?>" aria-label="Next">
                Next <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>