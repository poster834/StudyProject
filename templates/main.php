<main class="container">
    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <!--заполните этот список из массива категорий-->
            <?php foreach($category as $cat):?>
                <li class="promo__item promo__item--<?=$cat['type'];?>">
                    <a class="promo__link" href="all_lots.php?category_id=<?=$cat['id'];?>"><?=$cat['name'];?></a>
                </li>
            <?php endforeach;?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <!--заполните этот список из массива с товарами-->
            <?php foreach($lots as $key => $lot):?>
                <li class="lots__item lot">
                <div class="lot__image">
                    <img src="uploads/<?=filterXSS($lot['lot_img']);?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=$lot['category']?></span>
                    <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=$lot['lot_id']?>"><?=filterXSS($lot['name'])?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">стартовая цена</span>
                            <span class="lot__cost"><?=priceFormatter(filterXSS($lot['price']));?><b class='rub'>p.</b></span>
                        </div>
                        <?php if (get_dt_range($lot['date_finish'])['hours'] < 1): ?>
                            <div class="lot__timer timer timer--finishing">
                            <?=get_dt_range($lot['date_finish'])['hours'].":".get_dt_range($lot['date_finish'])['minutes'];?>
                            </div>
                        <?php else:?>
                            <div class="lot__timer timer">
                                <?=get_dt_range($lot['date_finish'])['hours'].":".get_dt_range($lot['date_finish'])['minutes'];?>
                            </div>
                        <?php endif;?>
                    </div>
                </div>
            </li>
            <?php endforeach;?>
        </ul>
    </section>
</main>