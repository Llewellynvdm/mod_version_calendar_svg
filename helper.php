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

use Joomla\Registry\Registry;

class ModVersion_Calendar_svgHelper
{
	/**
	 * The Module Params
	 *
	 * @var    Registry
	 * @since  1.0
	 */
	protected $params;

	/**
	 * The Years
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $years;

	/**
	 * The Branches
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $branches;

	/**
	 * The Width
	 *
	 * @var    int
	 * @since  1.0
	 */
	protected $width;

	/**
	 * The Height
	 *
	 * @var    int
	 * @since  1.0
	 */
	protected $height;

	/**
	 * Constructor
	 *
	 * @param Registry  $params  The module params
	 *
	 * @since 1.0.0
	 */
	public function __construct(Registry $params)
	{
		$this->params = $params;
	}

	/**
	 * Get Years
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function years(): array
	{
		if (empty($this->years))
		{
			$this->years = iterator_to_array(
				new DatePeriod(
					$this->min(),
					new DateInterval('P1Y'),
					$this->max()
				)
			);
		}

		return $this->years;
	}

	/**
	 * Get Width
	 *
	 * @return int
	 * @since 1.0.0
	 */
	public function width(): int
	{
		if (empty($this->width))
		{
			$years = $this->years();

			$this->width = $this->params->get('margin_left', 80) + 
				$this->params->get('margin_right', 50) +
				((count($years) - 1) * $this->params->get('year_width', 120));
		}

		return $this->width;
	}

	/**
	 * Get Height
	 *
	 * @return int
	 * @since 1.0.0
	 */
	public function height(): int
	{
		if (empty($this->height))
		{
			$branches = $this->branches();

			$this->height = $this->params->get('header_height',  24) +
				$this->params->get('footer_height', 24) +
				(count($branches) * $this->params->get('branch_height', 30));
		}

		return $this->height;
	}

	/**
	 * Get Branches
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function branches(): array
	{
		if (empty($this->branches))
		{
			$branches = (array) $this->params->get('dates',  null);

			if (is_array($branches) && count($branches) > 0)
			{
				$branch_height = $this->params->get('branch_height', 30);
				$header_height = $this->params->get('header_height',  24);

				$i = 0;

				foreach ($branches as $k => &$branch)
				{
					$branch->top = $header_height + ($branch_height * $i++);
				}

				$this->branches = $branches;
			}
			else
			{
				// we should add exception here
			}
		}

		return $this->branches;
	}

	/**
	 * Current state of a branch
	 *
	 * @param stdClass  $branch  The branch values
	 *
	 * @return string|null
	 * @since 1.0.0
	 */
	public function state(stdClass $branch): ?string
	{
		$initial = new DateTime($branch->start);
		$security = new DateTime($branch->security);
		$end = new DateTime($branch->end);

		if ($initial && $security)
		{
			$now = new DateTime;

			if ($now >= $end)
			{
				return 'eol';
			}

			if ($security && $now >= $security)
			{
				return 'security';
			}

			if ($now >= $initial)
			{
				return 'stable';
			}

			return 'future';
		}

		return null;
	}

	/**
	 * Minimum Number of Years
	 *
	 * @return ?
	 * @since 1.0.0
	 */
	public function min()
	{
		$now = new DateTime('January 1');
		return $now->sub(new DateInterval('P' .
			$this->params->get('min_years', 3) . 'Y'));
	}

	/**
	 * Maximum Number of Years
	 *
	 * @return ?
	 * @since 1.0.0
	 */
	public function max()
	{
		$now = new DateTime('January 1');
		return $now->add(new DateInterval('P' .
			$this->params->get('max_years', 3) . 'Y'));
	}

	/**
	 * The coordinates of this date
	 *
	 * @param DateTime $date The branch state date
	 *
	 * @return float
	 * @since 1.0.0
	 */
	public function coordinates(DateTime $date): float
	{
		$diff = $date->diff($this->min());

		if (!$diff->invert)
		{
			return $this->params->get('margin_left', 80);
		}

		return $this->params->get('margin_left', 80) +
			($diff->days /
				(365.24 / $this->params->get('year_width', 120))
			);
	}

}
