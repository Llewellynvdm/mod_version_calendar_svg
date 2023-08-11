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
		g.vcs-future rect,
		.vcs-branches rect.vcs-future {
			fill: <?php echo $params->get('future_color', '#5091cd'); ?>;
		}
		g.vcs-future text {
			fill: <?php echo $params->get('future_text_color', '#fff'); ?>;
		}
		g.vcs-eol rect,
		.vcs-branches rect.vcs-eol {
			fill: <?php echo $params->get('end_of_life_color', '#f33'); ?>;
		}
		g.vcs-eol text {
			fill: <?php echo $params->get('end_of_life_text_color', '#fff'); ?>;
		}
		<?php foreach ($branches as $version): ?>
			<?php foreach ($version->dates as $date): ?>
				g.<?php echo $date->state; ?> rect,
				.vcs-branches rect.<?php echo $date->state; ?> {
					fill: <?php echo $date->color; ?>;
				}
			<?php endforeach; ?>
		<?php endforeach; ?>
		.vcs-branch-labels text {
		dominant-baseline: central;
			text-anchor: middle;
		}
		.vcs-today line {
			stroke: <?php echo $params->get('today_line_color', '#f33'); ?>;
			stroke-dasharray: 7, 7;
			stroke-width: 3px;
		}
		.vcs-today text {
			fill: <?php echo $params->get('today_text_color', '#f33'); ?>;
			text-anchor: middle;
		}
		.vcs-years line {
			stroke: <?php echo $params->get('years_line_color', '#000'); ?>;
		}
		.vcs-years text {
			fill: <?php echo $params->get('years_text_color', '#000'); ?>;
			text-anchor: middle;
		}
	</style>
	<!-- Branch labels -->
	<g class="vcs-branch-labels">
		<?php foreach ($branches as $key => $branch): ?>
			<g class="<?php echo $helper->state($branch->dates); ?>">
				<rect x="0" y="<?php echo $branch->top; ?>" width="<?php echo 0.5 * $params->get('margin_left', 80); ?>"
					height="<?php echo $params->get('branch_height', 30); ?>"/>
				<text x="<?php echo 0.25 * $params->get('margin_left', 80); ?>" y="<?php echo $branch->top + (0.5 * $params->get('branch_height', 30)); ?>">
					<?php echo htmlspecialchars($branch->version); ?>
				</text>
			</g>
		<?php endforeach; ?>
	</g>
	<!-- Branch blocks -->
	<g class="vcs-branches">
		<?php foreach ($branches as $key => $version): ?>
			<?php
				$y = $version->top;
				$height = $params->get('branch_height', 30);
			?>
			<?php foreach ($version->dates as $date): ?>
				<?php
					$x_start = $helper->coordinates(new DateTime($date->start));
					$x_end = $helper->coordinates(new DateTime($date->end));
				?>
				<g class="<?php echo $date->state; ?>">
					<rect
						x="<?php echo $x_start; ?>"
						y="<?php echo $y; ?>"
						width="<?php echo $x_end - $x_start; ?>"
						height="<?php echo $height; ?>">
							<title><?php echo htmlspecialchars($date->label); ?></title>
					</rect>
				</g>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</g>
	<!-- Year lines -->
	<g class="vcs-years">
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
	<g class="vcs-today">
		<?php
			$now = new DateTime;
			$x = $helper->coordinates($now);
		?>
		<line x1="<?php echo $x; ?>" y1="<?php echo $params->get('header_height', 24); ?>" x2="<?php echo $x; ?>"
			y2="<?php echo $params->get('header_height', 24) + ($qty * $params->get('branch_height', 30)); ?>"/>
		<text x="<?php echo $x; ?>"
			y="<?php echo $params->get('header_height', 24) + ($qty * $params->get('branch_height', 30)) + (0.8 * $params->get('footer_height', 24)); ?>">
			<?php echo JText::_('MOD_VERSION_CALENDAR_SVG_TODAY') . ': ' . $now->format('j M Y'); ?>
		</text>
	</g>
</svg>
<?php if ($params->get('show_legend', 0) == 1): ?>
<?php 
// get the legend values
$legend = $helper->legend();
?>
<style type="text/css">
	/* Box Shadow */
	.vcs-box-shadow-medium {
		box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15), 0 0.5rem 1.5rem rgba(0, 0, 0, 0.25);
	}
	/* Card Styles */
	.vcs-card {
		border-radius: 4px;
		margin-top: 15px;
	}
	.vcs-card-legend {
		background-color: <?php echo $params->get('legend_background_color', '#494444'); ?>;
		color: <?php echo $params->get('legend_text_color', '#fbf3ef'); ?>;
	}
	.vcs-card-body {
		padding: 4px;
	}
	/* Grid Styles */
	.vcs-grid {
		display: flex;
		flex-wrap: wrap;
	}
	.vcs-grid-match > div {
		padding: 5px;
		min-height: 1px;
		margin: 10px;
	}
	/* Flexbox Styles */
	.vcs-flex {
		display: flex;
		align-items: center;
		justify-content: space-between;
	}
	.vcs-flex-middle {
		align-items: center;
	}
	/* Color Box Styles */
	.vcs-color-box {
		width: 20px;
		height: 20px;
		display: inline-block;
		margin-right: 5px;
	}
	.vcs-future { background-color: <?php echo $params->get('future_color', '#000'); ?>; }
	.vcs-eol { background-color: <?php echo $params->get('end_of_life_color', '#f33'); ?>; }
	<?php foreach ($legend as $state): ?>
		.<?php echo $state->state; ?> { background-color: <?php echo $state->color; ?>; }
	<?php endforeach; ?>
	/* Media Query for smaller screens */
	@media (max-width: 768px) {
		.vcs-grid {
			flex-direction: column;
		}
		.vcs-flex {
			display: block;
		}
		.vcs-grid-match > div {
			margin: 4px;
			padding: 0;
		}
	}
</style>
<div class="vcs-box-shadow-medium">
	<div class="vcs-card vcs-card-legend vcs-card-body">
		<div class="vcs-grid-match vcs-grid">
			<div class="vcs-flex vcs-flex-middle">
				<span
					class="vcs-color-box vcs-future hasTooltip"
					title="<?php echo JText::_('MOD_VERSION_CALENDAR_SVG_PLANNED_RELEASE_SCHEDULE'); ?>"
				></span><?php echo JText::_('MOD_VERSION_CALENDAR_SVG_FUTURE_RELEASES'); ?>
			</div>
			<?php foreach ($legend as $state): ?>
				<div class="vcs-flex vcs-flex-middle">
					<span
						class="vcs-color-box <?php echo $state->state; ?> hasTooltip"
						title="<?php echo $state->description ?? ''; ?>"
					></span><?php echo $state->label; ?>
				</div>
			<?php endforeach; ?>
			<div class="vcs-flex vcs-flex-middle">
				<span
					class="vcs-color-box vcs-eol hasTooltip"
					title="<?php echo JText::_('MOD_VERSION_CALENDAR_SVG_VERSION_END_OF_LIFE_SCHEDULE_EXPECT_NO_MORE_SUPPORT'); ?>"
				></span><?php echo JText::_('MOD_VERSION_CALENDAR_SVG_VERSION_AT_END_OF_LIFE'); ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
