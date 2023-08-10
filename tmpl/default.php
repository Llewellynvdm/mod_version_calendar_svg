<?php
/**
 * @package    Joomla.CMS
 * @maintainer Llewellyn van der Merwe <https://git.vdm.dev/Llewellyn>
 *
 * @created    29th July, 2020
 * @copyright  (C) 2020 Open Source Matters, Inc. <http://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


?>
<svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 <?php echo $helper->width(); ?> <?php echo $helper->height(); ?>"
	width="<?php echo $helper->width(); ?>" height="<?php echo $helper->height(); ?>">
	<style type="text/css">
		text {
			fill: <?php echo $params->get('text_color', '#333'); ?>;
			font-family: "Source Sans Pro", Helvetica, Arial, sans-serif;
			font-size: <?php echo (2 / 3) * $params->get('header_height', 24); ?>px;
		}

		g.future rect,
		.branches rect.future {
			fill: <?php echo $params->get('future_color', '#000'); ?>;
		}

		g.eol rect,
		.branches rect.eol {
			fill: <?php echo $params->get('end_of_life_color', '#f33'); ?>;
		}

		g.eol text {
			fill: <?php echo $params->get('end_of_life_text_color', '#fff'); ?>;
		}

		g.security rect,
		.branches rect.security {
			fill: <?php echo $params->get('security_color', '#f93'); ?>;
		}

		g.stable rect,
		.branches rect.stable {
			fill: <?php echo $params->get('stable_color', '#9c9'); ?>;
		}

		.branch-labels text {
		dominant-baseline: central;
			text-anchor: middle;
		}

		.today line {
			stroke: <?php echo $params->get('today_line_color', '#f33'); ?>;
			stroke-dasharray: 7, 7;
			stroke-width: 3px;
		}

		.today text {
			fill: <?php echo $params->get('today_text_color', '#f33'); ?>;
			text-anchor: middle;
		}

		.years line {
			stroke: <?php echo $params->get('years_line_color', '#000'); ?>;
		}

		.years text {
			fill: <?php echo $params->get('years_text_color', '#000'); ?>;
			text-anchor: middle;
		}
	</style>

	<!-- Branch labels -->
	<g class="branch-labels">
		<?php foreach ($branches as $branch): ?>
			<g class="<?php echo $helper->state($branch); ?>">
				<rect x="0" y="<?php echo $branch->top; ?>" width="<?php echo 0.5 * $params->get('margin_left', 80); ?>"
					height="<?php echo $params->get('branch_height', 30); ?>"/>
				<text x="<?php echo 0.25 * $params->get('margin_left', 80); ?>" y="<?php echo $branch->top + (0.5 * $params->get('branch_height', 30)); ?>">
					<?php echo htmlspecialchars($branch->version); ?>
				</text>
			</g>
		<?php endforeach; ?>
	</g>

	<!-- Branch blocks -->
	<g class="branches">
		<?php foreach ($branches as $branch): ?>
			<?php
				$x_release = $helper->coordinates(new DateTime($branch->start));
				$x_eol = $helper->coordinates(new DateTime($branch->end));
				$x_security = (empty($branch->security)) ? $x_eol : $helper->coordinates(new DateTime($branch->security));
			?>
			<rect class="stable" x="<?php echo $x_release; ?>" y="<?php echo $branch->top; ?>"
				width="<?php echo $x_security - $x_release; ?>" height="<?php echo $params->get('branch_height', 30); ?>"/>
			<rect class="security" x="<?php echo $x_security; ?>" y="<?php echo $branch->top; ?>"
				width="<?php echo $x_eol - $x_security; ?>" height="<?php echo $params->get('branch_height', 30); ?>"/>
		<?php endforeach; ?>
	</g>

	<!-- Year lines -->
	<g class="years">
		<?php foreach ($helper->years() as $date): ?>
			<line x1="<?php echo $helper->coordinates($date); ?>" y1="<?php echo $params->get('header_height', 24); ?>"
				x2="<?php echo $helper->coordinates($date); ?>"
				y2="<?php echo $params->get('header_height', 24) + ($qty * $params->get('branch_height', 30)); ?>"/>
			<text x="<?php echo $helper->coordinates($date) ;?>" y="<?php echo 0.8 * $params->get('header_height', 24); ?>">
				<?php echo $date->format('j M Y'); ?>
			</text>
		<?php endforeach; ?>
	</g>

	<!-- Today -->
	<g class="today">
		<?php
			$now = new DateTime;
			$x = $helper->coordinates($now);
		?>
		<line x1="<?php echo $x; ?>" y1="<?php echo $params->get('header_height', 24); ?>" x2="<?php echo $x; ?>"
			y2="<?php echo $params->get('header_height', 24) + ($qty * $params->get('branch_height', 30)); ?>"/>
		<text x="<?php echo $x; ?>"
			y="<?php echo $params->get('header_height', 24) + ($qty * $params->get('branch_height', 30)) + (0.8 * $params->get('footer_height', 24)); ?>">
			<?php echo 'Today: ' . $now->format('j M Y'); ?>
		</text>
	</g>
</svg>
