<?php if($paginator->page_count > 1) { ?>
<div id="paginator-inside">
    <?php if ($paginator->current_page > 1) { ?>
        <a href="<?php echo \Helpers\get_GET_params($paginator->existing_params, ['page' => $paginator->current_page - 1]) ?>" class="pag-arrow pag-prev">
            <img src="/diplom/images/left.png" alt="Назад">
        </a>
    <?php } else { ?>
        <span class="pag-arrow pag-disabled">
            <img src="/diplom/images/left.png" alt="Назад">
        </span>
    <?php } ?>
    
    <?php if ($paginator->current_page < $paginator->page_count) { ?>
        <a href="<?php echo \Helpers\get_GET_params($paginator->existing_params, ['page' => $paginator->current_page + 1]) ?>" class="pag-arrow pag-next">
            <img src="/diplom/images/right.png" alt="Вперёд">
        </a>
    <?php } else { ?>
        <span class="pag-arrow pag-disabled">
            <img src="/diplom/images/right.png" alt="Вперёд">
        </span>
    <?php } ?>
</div> 
<?php } ?>