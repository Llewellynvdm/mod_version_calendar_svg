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
		<?php $active = []; ?>
		<?php foreach ($branches as $branch): ?>
			<?php $state = $helper->state($branch); ?>
			<?php $active[$state] = $state; ?>
			<g class="<?php echo $state; ?>">
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
<?php if ($params->get('show_legend', 0) == 1): ?>
<style type="text/css">
	/* Box Shadow */
	.vdm-box-shadow-medium {
		box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15), 0 0.5rem 1.5rem rgba(0, 0, 0, 0.25);
	}
	/* Card Styles */
	.vdm-card {
		border-radius: 4px;
		margin-top: 15px;
	}
	.vdm-card-legend {
		background-color: <?php echo $params->get('legend_background_color', '#494444'); ?>;
		color: <?php echo $params->get('legend_text_color', '#fbf3ef'); ?>;
	}
	.vdm-card-body {
		padding: 4px;
	}
	/* Grid Styles */
	.vdm-grid {
		display: flex;
		flex-wrap: wrap;
	}
	.vdm-grid-match > div {
		padding: 5px;
		min-height: 1px;
		margin: 10px;
	}
	/* Flexbox Styles */
	.vdm-flex {
		display: flex;
		align-items: center;
		justify-content: space-between;
	}
	.vdm-flex-middle {
		align-items: center;
	}
	/* Color Box Styles */
	.vdm-color-box {
		width: 20px;
		height: 20px;
		display: inline-block;
		margin-right: 5px;
	}
	.vdm-future { background-color: <?php echo $params->get('future_color', '#000'); ?>; }
	.vdm-stable { background-color: <?php echo $params->get('stable_color', '#9c9'); ?>; }
	.vdm-security { background-color: <?php echo $params->get('security_color', '#f93'); ?>; }
	.vdm-end-of-life { background-color: <?php echo $params->get('end_of_life_color', '#f33'); ?>; }
	/* Media Query for smaller screens */
	@media (max-width: 768px) {
		.vdm-grid {
			flex-direction: column;
		}
		.vdm-flex {
			display: block;
		}
		.vdm-grid-match > div {
			margin: 4px;
			padding: 0;
		}
	}
</style>
<div class="vdm-box-shadow-medium">
	<div class="vdm-card vdm-card-legend vdm-card-body">
		<div class="vdm-grid-match vdm-grid">
			<?php if (isset($active['future'])): ?>
				<div class="vdm-flex vdm-flex-middle">
					<span
						class="vdm-color-box vdm-future hasTooltip"
						title="<?php echo JText::_('MOD_VERSION_CALENDAR_SVG_PLANNED_RELEASE_SCHEDULE'); ?>"
					></span><?php echo JText::_('MOD_VERSION_CALENDAR_SVG_FUTURE_RELEASES'); ?>
				</div>
			<?php endif; ?>
			<?php if (isset($active['stable'])): ?>
				<div class="vdm-flex vdm-flex-middle">
					<span
						class="vdm-color-box vdm-stable hasTooltip"
						title="<?php echo JText::_('MOD_VERSION_CALENDAR_SVG_STABLE_RELEASE_SCHEDULE_EXPECT_FULL_SUPPORT_AND_UPDATES'); ?>"
					></span><?php echo JText::_('MOD_VERSION_CALENDAR_SVG_STABLE_RELEASE'); ?>
				</div>
			<?php endif; ?>
			<?php if (isset($active['security'])): ?>
				<div class="vdm-flex vdm-flex-middle">
					<span
						class="vdm-color-box vdm-security hasTooltip"
						title="<?php echo JText::_('MOD_VERSION_CALENDAR_SVG_SECURITY_SCHEDULE_EXPECT_ONLY_SECURITY_UPDATES'); ?>"
					></span><?php echo JText::_('MOD_VERSION_CALENDAR_SVG_SECURITY_RELEASE'); ?>
				</div>
			<?php endif; ?>
			<?php if (isset($active['eol'])): ?>
				<div class="vdm-flex vdm-flex-middle">
					<span
						class="vdm-color-box vdm-end-of-life hasTooltip"
						title="<?php echo JText::_('MOD_VERSION_CALENDAR_SVG_VERSION_END_OF_LIFE_SCHEDULE_EXPECT_NO_MORE_SUPPORT'); ?>"
					></span><?php echo JText::_('MOD_VERSION_CALENDAR_SVG_VERSION_AT_END_OF_LIFE'); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php endif; ?>
