<?php
/*
Template Name: Custom Post Archive
*/
get_header(); ?>

<div class="container">
  <div class="row">
    <div class="col-md-8">

      <h1><?php the_title(); ?></h1>

      <?php
      // Get the current date
      $current_date = date('Y-m-d');

      // Set up the query to get posts by date and category
      $args = array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
        'date_query' => array(
          array(
            'after' => $current_date,
            'inclusive' => true,
          ),
        ),
      );

      // Check if a category filter is set
      if (isset($_GET['category'])) {
        $args['category_name'] = sanitize_text_field($_GET['category']);
      }

      // Query the posts
      $query = new WP_Query($args);

      // Check if any posts were found
      if ($query->have_posts()) {
        // Display the posts
        while ($query->have_posts()) {
          $query->the_post();
          ?>

          <div class="post">
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <p class="post-meta">
              Posted on <?php the_date(); ?> in <?php the_category(', '); ?>
            </p>
            <div class="post-content">
              <?php the_excerpt(); ?>
            </div>
          </div>

          <?php
        }
        wp_reset_postdata();
      } else {
        // Display a message if no posts were found
        echo '<p>No posts found.</p>';
      }
      ?>

    </div>
    <div class="col-md-4">

      <h2>Filter Posts</h2>

      <form method="get" action="<?php echo esc_url(get_permalink()); ?>">
        <div class="form-group">
          <label for="category">Category:</label>
          <?php
          // Get a list of categories
          $categories = get_categories(array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 1,
          ));
          ?>
          <select name="category" id="category" class="form-control">
            <option value="">All Categories</option>
            <?php foreach ($categories as $category) : ?>
              <option value="<?php echo esc_attr($category->slug); ?>" <?php selected($category->slug, isset($_GET['category']) ? $_GET['category'] : ''); ?>><?php echo esc_html($category->name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="date">Date:</label>
          <input type="date" name="date" id="date" class="form-control" value="<?php echo esc_attr(isset($_GET['date']) ? $_GET['date'] : ''); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
      </form>

    </div>
  </div>
</div>

<?php get_footer(); ?>