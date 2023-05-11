<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 *
 * @since  1.6
 */
class JFormFieldTimecreated extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'timecreated';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string    The field input markup.
	 *
	 * @since    1.6
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = [];
		$time_created = $this->value;
		if (!strtotime($time_created)) {
			$time_created = JHtml:: date ('now', 'Y-m-d h:i:s', true);
			$html[]       = '<input type="hidden" name="' . $this->name . '" value="' . $time_created . '" />';
		}

		$hidden = (boolean) $this->element['hidden'];

		if (!$hidden)
		{
			$jdate       = new JDate($time_created);
			$pretty_date = $jdate->format(JText::_('DATE_FORMAT_LC2'));
			$html[]      = "<div>" . $pretty_date . "</div>";
		}

		return implode($html);
	}
}
