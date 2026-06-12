<?php if (($totalPages ?? 0) > 1): ?>
<nav>
    <ul class="pagination justify-content-center">
        <li class="page-item <?= ($page ?? 1) <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => ($page ?? 1) - 1])) ?>">Previous</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($page ?? 1) === $i ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= ($page ?? 1) >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => ($page ?? 1) + 1])) ?>">Next</a>
        </li>
    </ul>
</nav>
<?php endif; ?>
