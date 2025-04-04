<?php
/**
 * @package WordPress
 * @subpackage vanderbilt brand
 */

get_header();
?>
<div class="panel panel-default col-sm-9">
    <div class="row">
        <article class="primary-content col-sm-12">
            <div class="panel-body">
                <?php $blog_details = get_blog_details(); 

                ?>
                <h2> <?php echo _($blog_details->blogname); ?> Lab Members </h2>
                <?php

                $args = array(
                    'post_type' => 'lab_people',
                    'depth' => 1,
                    'posts_per_page' => -1,
                    'post_status' => array('publish'),
                    'meta_key' => 'last_family_name',
                    'orderby' => 'meta_value',
                    'order' => 'ASC',
                );

                $wp_query = new WP_Query($args);
                ?>
                <div class="row">
                    <?php if($wp_query->have_posts()):
                        while($wp_query->have_posts()):

                            $wp_query->the_post();
                            ?>

                            <div class="col-xs-6 col-sm-4 col-md-3 people-swatch">
                                <div class="people-photo">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('img-150-150'); ?>
                                    </a>
                                </div>
                                <div class="people-stats">
                                    <h4 class="people-name">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_field('first_given_name'); ?> <?php the_field('middle_name_or_initial'); ?> <?php the_field('last_family_name'); ?>
                                            <?php if (get_field('suffix__credentials')) echo ', ' . get_field('suffix__credentials'); ?>
                                        </a>
                                    </h4>
                                    <div class="people-title">

                                        <?php if(have_rows('title_and_department')): ?>
                                            <?php while(have_rows('title_and_department')): the_row(); ?>
                                                <?php if(get_sub_field('title__position')): ?>
                                                    <?php the_sub_field('title__position'); ?>
                                                        <?php if(get_sub_field('department__center__office')): ?>
                                                            , <?php the_sub_field('department__center__office'); ?>
                                                        <?php endif; ?>
                                                     <br />
                                                    <?php
                                                        // break here because we don't want to list out all the title in a small space
                                                        break;
                                                    ?>
                                                <?php endif; ?>
                                            <?php endwhile; ?>
                                        <?php endif; ?>

                                    </div>
                                    <div class="people-email">
                                        <?php if(get_field('email')): ?>
                                            <a href="mailto:<?php the_field('email'); ?>"> <i class="fa fa-envelope" aria-hidden="true"></i> <?php the_field('email'); ?> </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile;
                    endif;
                    ?>
                </div>

                <?php wp_reset_query(); ?>
            </div>
        </article>
    </div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
