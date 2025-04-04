<?php
/*
 * Template Name: Lab Person Page
 */

get_header(); ?>

    <div class="panel panel-default col-sm-9">
        <div class="row">
            <article class="primary-content col-sm-12">
                <div class="panel-body">
                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                        <?php edit_post_link( __( 'Edit', 'vanderbilt_brand' ), '<span class="edit-link">', '</span>' ); ?>


                        <!-- information section -->
                        <div class="">
                            <div class="pull-right" style="margin: 0 0 0 15px;">
                                <?php the_post_thumbnail(array(300,300), array("class" => "media-object img-thumbnail")); ?>
                            </div>
                            <div class="lab-person-info">
                                <h3 class="pagetitle"><?php the_title(); ?></h3>
                                <p>
                                    <?php if(have_rows('title_and_department')): ?>
                                        <?php while(have_rows('title_and_department')):
                                            the_row();

                                            $have_title = 0;
                                            $have_office = 0;

                                            if(get_sub_field('title__position')) {
                                                $shortcode_string .= get_sub_field('title__position');
                                                $have_title = 1;
                                            }

                                            if($have_title && get_sub_field('department__center__office')){
                                                $shortcode_string .= ', ' . get_sub_field('department__center__office') . '<br />';
                                                $have_office = 1;
                                            } else if (get_sub_field('department__center__office')) {
                                                $shortcode_string .= get_sub_field('department__center__office') . '<br />';
                                                $have_office = 1;
                                            }

                                            if($have_title && !$have_office){
                                                $shortcode_string .= '<br />';
                                            }
                                            ?>


                                            <?php echo $shortcode_string;
                                            $shortcode_string = '';
                                            ?>
                                            <!--                                            --><?php //the_sub_field('title__position'); ?><!--, --><?php //the_sub_field('department__center__office'); ?>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </p>
                                <hr />

                                <ul>
                                    <?php if(get_field('email')): ?>
                                        <li> <i class="fa fa-envelope" aria-hidden="true"></i> : <a href="mailto:<?php the_field('email'); ?>"> <?php the_field('email'); ?> </a></li>
                                    <?php endif; ?>
                                    <?php if(have_rows('phone_numbers')): ?>
                                        <?php while(have_rows('phone_numbers')): the_row(); ?>
                                            <?php if(get_sub_field('number')): ?>
                                                <li> <i class="fa fa-phone" aria-hidden="true"></i> : <a href="tel:<?php the_sub_field('number'); ?>"> <?php the_sub_field('number') ?> </a> </li>
                                            <?php endif; ?>
                                        <?php endwhile; ?>
                                    <?php endif; ?>

                                    <?php if(have_rows('address')): ?>
                                        <?php while(have_rows('address')): the_row(); ?>
                                            <?php if(get_sub_field('building') || get_sub_field('street_address')): ?>
                                                <li> <i class="fa fa-building-o" aria-hidden="true"></i> :

                                                    <?php if(get_sub_field('room__suite') || get_sub_field('building')): ?>
                                                        <?php the_sub_field('room__suite'); ?> <?php the_sub_field('building'); ?> <br />
                                                    <?php endif; ?>

                                                    <?php if(get_sub_field('street_address')): ?>
                                                        <?php the_sub_field('street_address'); ?> <br />
                                                    <?php endif; ?>
                                                    <?php if(get_sub_field('street_address_2')): ?>
                                                        <?php the_sub_field('street_address_2'); ?> <br />
                                                    <?php endif; ?>
                                                    <?php if(get_sub_field('city')): ?>
                                                        <?php the_sub_field('city'); ?>, <?php the_sub_field('state'); ?> - <?php the_sub_field('zip'); ?>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endif; ?>
                                        <?php endwhile; ?>
                                    <?php endif; ?>

                                    <?php if(get_field('more_information_link') && !empty(get_field('more_information_link'))): ?>
                                        <li>
                                            <i class="fa fa-globe" aria-hidden="true"></i> : <a href="<?php the_field('more_information_link'); ?>" target="_blank"> More Information </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if(get_field('cv')): ?>
                                        <li>
                                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> : <a href="<?php the_field('cv'); ?>" target="_blank"> <?php the_title(); ?> - CV </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>

                                <!-- Show Bio -->
                                <div class="">
                                    <?php if(get_field('brief_description')): ?>
                                        <?php the_field('brief_description'); ?>
                                    <?php endif; ?>
                                </div>

                                <!-- Show Bio -->
                                <div class="">
                                    <?php if(get_field('bio__about')): ?>
                                        <?php the_field('bio__about'); ?>
                                    <?php endif; ?>
                                </div>

                            </div>


                        </div>




                        <div class="">
                            <?php //the_content(); ?>
                        </div>



                        <hr />

                        <?php if (get_theme_mod('socialsharelinks') == true) { ?>
                            <div class="addthis_sharing_toolbox"></div>
                        <?php } ?>

                        <?php comments_template(); ?>


                    <?php endwhile; else: ?>

                        <p>Sorry, no posts matched your criteria.</p>

                    <?php endif; ?>

                </div>
            </article>
        </div>
    </div>

<?php wp_reset_query(); ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>