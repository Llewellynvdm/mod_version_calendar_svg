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
	 * The Legend
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $legend;

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
	 * Fetches and processes the branches or versions from the parameters. 
	 * It sanitizes the branch data, calculates their positions, sorts them, and then returns.
	 * If no valid branches or versions are found, it throws an exception.
	 *
	 * @return array
	 *
	 * @since 2.0.1
	 * @throws Exception If no valid branches or versions are found.
	 */
	public function branches(): array
	{
		if (empty($this->branches))
		{
			$branches = (array) $this->params->get('versions');

			if (empty($branches))
			{
				throw new Exception("No versions found.");
			}
			$this->sanitize($branches);

			if (empty($branches))
			{
				throw new Exception("No versions found.");
			}

			$this->setTop($branches);
			$this->sort($branches);

			$this->branches = $branches;
		}

		return $this->branches;
	}

	/**
	 * Get Legend values (by color)
	 *
	 * @return array
	 * @since 2.0.1
	 */
	public function legend(): array
	{
		if (empty($this->legend))
		{
			$branches = $this->branches();

			foreach ($branches as $version)
			{
				foreach ($version->dates as $date)
				{
					$this->legend[$date->color] = $date;
				}
			}
		}

		return $this->legend;
	}

	/**
	 * Current state of a branch
	 *
	 * @param array  $dates  The branch dates
	 *
	 * @return string|null
	 * @since 2.0.1
	 */
	public function state(array $dates): ?string
	{
		// Determine the current state.
		$now = new DateTime();

		// Check if today's date is before the earliest start date.
		$earliestDate = DateTime::createFromFormat('d-m-Y', $dates[0]->start);
		if ($now < $earliestDate)
		{
			return 'vcs-future';
		}

		// Check if today's date is after the latest end date.
		$latestDate = DateTime::createFromFormat('d-m-Y', end($dates)->end);
		if ($now > $latestDate)
		{
			return 'vcs-eol';
		}

		// Determine which state the current date falls under.
		foreach ($dates as $date)
		{
			$initial = DateTime::createFromFormat('d-m-Y', $date->start);
			$end = DateTime::createFromFormat('d-m-Y', $date->end);

			if ($now >= $initial && $now <= $end)
			{
				return $date->state;
			}
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

	/**
	 * Sort Branches state's by date
	 *
	 * @param array  $branches  The branches
	 *
	 * @return void
	 * @since 2.0.1
	 */
	protected function sort(array &$branches): void
	{
		foreach ($branches as $key => &$branch)
		{
			usort($branch->dates, function($a, $b) {
				$startDateA = DateTime::createFromFormat('d-m-Y', $a->start);
				$startDateB = DateTime::createFromFormat('d-m-Y', $b->start);

				if ($startDateA == $startDateB)
				{
					$endDateA = DateTime::createFromFormat('d-m-Y', $a->end);
					$endDateB = DateTime::createFromFormat('d-m-Y', $b->end);
					return $endDateA <=> $endDateB;
				}

				return $startDateA <=> $startDateB;
			});
		}
	}

	/**
	 * Set Top
	 *
	 * Calculates the top position for each branch based on parameters for branch height and header height.
	 *
	 * @param array $branches Reference to the branches array.
	 *
	 * @return void
	 * @since 2.0.1
	 */
	protected function setTop(array &$branches): void
	{
		$branch_height = $this->params->get('branch_height', 30);
		$header_height = $this->params->get('header_height', 24);

		$i = 0;
		foreach ($branches as $key => &$branch)
		{
			$branch->top = $header_height + ($branch_height * $i++);
		}
	}

	/**
	 * Sanitize
	 *
	 * Sanitizes the branches by checking the existence and type of 'dates' and 'date->state'. 
	 * Also modifies the state of each date entry within a branch.
	 *
	 * @param array $branches Reference to the branches array.
	 *
	 * @return void
	 * @since 2.0.1
	 */
	protected function sanitize(array &$branches): void
	{
		foreach ($branches as $key => &$branch)
		{
			if (empty($branch->dates) || !is_object($branch->dates))
			{
				unset($branches[$key]);
				continue;
			}

			$branch->dates = (array) $branch->dates;

			$remove = false;
			foreach ($branch->dates as $k => &$date)
			{
				if (empty($date->state))
				{
					$remove = true;
					continue;
				}
				$date->state = $this->makeSafe($key . '-' . $date->state);
			}

			if ($remove)
			{
				unset($branches[$key]);
			}
		}
	}

	/**
	 * Get css safe class name
	 *
	 * @param string  $name  The string to make safe
	 *
	 * @return string
	 * @since 2.0.1
	 */
	protected function makeSafe(string $name): string
	{
		// Ensure it doesn't start with a digit
		if (preg_match('/^[0-9]/', $name))
		{
			$name = 'vcs-' . $name;
		}

		// Replace any non-alphanumeric characters with hyphens
		$name = preg_replace('/[^a-zA-Z0-9]+/', '-', $name);

		// Convert to lowercase
		$name = strtolower($name);

		return $name;
	}
}
