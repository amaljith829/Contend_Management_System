<?php

class Paginator {
    public $limit;
    public $offset;
    public $previous;
    public $next;

    public function __construct( $page, $recordsPerPage, $totalRecords) {
        $this->limit = $recordsPerPage;
        $page = filter_var($page, FILTER_VALIDATE_INT, ['options' => [ 'default' => 1, 'min_range' => 1]]);
        if($page > 1) {
            $this->previous = $page - 1;
        }
        $totalPages = ceil($totalRecords / $recordsPerPage);
        if($page < $totalPages) {
            $this->next = $page + 1;
        }
        $this->offset = ($page - 1) * $recordsPerPage;
    }
}