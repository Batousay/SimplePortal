<?php

/**
 * @package SimplePortal
 *
 * @author SimplePortal Team
 * @copyright 2013 SimplePortal Team
 * @license BSD 3-clause 
 *
 * @version 2.4
 */

function template_articles_list()
{
	global $context, $scripturl, $settings, $txt;

	echo '
	<div id="sp_manage_articles">
		<form action="', $scripturl, '?action=admin;area=portalarticles;sa=list" method="post" accept-charset="', $context['character_set'], '" onsubmit="return confirm(\'', $txt['sp_articles_remove_confirm'], '\');">
			<div class="sp_align_left pagesection">
				', $txt['pages'], ': ', $context['page_index'], '
			</div>
			<table class="table_grid" cellspacing="0" width="100%">
				<thead>
					<tr class="catbg">';

	foreach ($context['columns'] as $column)
	{
		if ($column['selected'])
			echo '
						<th scope="col"', isset($column['class']) ? ' class="' . $column['class'] . '"' : '', isset($column['width']) ? ' width="' . $column['width'] . '"' : '', '>
							<a href="', $column['href'], '">', $column['label'], '&nbsp;<img src="', $settings['images_url'], '/sort_', $context['sort_direction'], '.gif" alt="" /></a>
						</th>';
		elseif ($column['sortable'])
			echo '
						<th scope="col"', isset($column['class']) ? ' class="' . $column['class'] . '"' : '', isset($column['width']) ? ' width="' . $column['width'] . '"' : '', '>
							', $column['link'], '
						</th>';
		else
			echo '
						<th scope="col"', isset($column['class']) ? ' class="' . $column['class'] . '"' : '', isset($column['width']) ? ' width="' . $column['width'] . '"' : '', '>
							', $column['label'], '
						</th>';
	}

	echo '
						<th scope="col" class="last_th">
							<input type="checkbox" class="input_check" onclick="invertAll(this, this.form);" />
						</th>
					</tr>
				</thead>
				<tbody>';
	
	if (empty($context['articles']))
	{
		echo '
					<tr class="windowbg2">
						<td class="sp_center" colspan="', count($context['columns']) + 1, '">', $txt['error_sp_no_articles'], '</td>
					</tr>';
	}

	foreach ($context['articles'] as $article)
	{
		echo '
					<tr class="windowbg2">
						<td class="sp_left">', $article['link'], '</td>
						<td class="sp_center">', $article['article_id'], '</td>
						<td class="sp_center">', $article['category']['link'], '</td>
						<td class="sp_center">', $article['author']['link'], '</td>
						<td class="sp_center">', $article['type_text'], '</td>
						<td class="sp_center">', $article['date'], '</td>
						<td class="sp_center">', $article['status_image'], '</td>
						<td class="sp_center">', implode('&nbsp;', $article['actions']), '</td>
						<td class="sp_center"><input type="checkbox" name="remove[]" value="', $article['id'], '" class="input_check" /></td>
					</tr>';
	}

	echo '
				</tbody>
			</table>
			<div class="sp_align_left pagesection">
				<div class="sp_float_right">
					<input type="submit" name="remove_articles" value="', $txt['sp_admin_articles_remove'], '" class="button_submit" />
				</div>
				', $txt['pages'], ': ', $context['page_index'], '
			</div>
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
		</form>
	</div>';
}

function template_articles_edit()
{
	global $context, $scripturl, $settings, $txt, $helptxt;

	if (!empty($context['preview']))
	{
		echo '
	<div class="sp_auto_align" style="width: 90%; padding-bottom: 1em;">';

		template_view_article();

		echo '
	</div>';
	}

	echo '
	<div id="sp_edit_article">
		<form action="', $scripturl, '?action=admin;area=portalarticles;sa=edit" method="post" accept-charset="', $context['character_set'], '" onsubmit="submitonce(this);">
			<div class="cat_bar">
				<h3 class="catbg">
					', $txt['sp_admin_articles_general'], '
				</h3>
			</div>
			<div class="windowbg2">
				<span class="topslice"><span></span></span>
				<div class="sp_content_padding">
					<dl class="sp_form">
						<dt>
							<label for="article_title">', $txt['sp_admin_articles_col_title'], ':</label>
						</dt>
						<dd>
						<input type="text" name="title" id="article_title" value="', $context['article']['title'], '" class="input_text" />
						</dd>
						<dt>
							<label for="article_namespace">', $txt['sp_admin_articles_col_namespace'], ':</label>
						</dt>
						<dd>
							<input type="text" name="namespace" id="article_namespace" value="', $context['article']['article_id'], '" class="input_text" />
						</dd>
						<dt>
							<label for="article_category">', $txt['sp_admin_articles_col_category'], ':</label>
						</dt>
						<dd>
							<select name="category_id" id="article_category">';

	foreach ($context['article']['categories'] as $category)
		echo '
								<option value="', $category['id'], '"', $context['article']['category']['id'] == $category['id'] ? ' selected="selected"' : '', '>', $category['name'], '</option>';

	echo '
							</select>
						</dd>
						<dt>
							<label for="article_type">', $txt['sp_admin_articles_col_type'], ':</label>
						</dt>
						<dd>
							<select name="type" id="article_type" onchange="sp_update_editor();">';

	$content_types = array('bbc', 'html', 'php');
	foreach ($content_types as $type)
		echo '
								<option value="', $type, '"', $context['article']['type'] == $type ? ' selected="selected"' : '', '>', $txt['sp_articles_type_' . $type], '</option>';

	echo '
							</select>
						</dd>
						<dt>
							<label for="article_permissions">', $txt['sp_admin_articles_col_permissions'], ':</label>
						</dt>
						<dd>
							<select name="permissions" id="article_permissions">';

	foreach ($context['article']['permission_profiles'] as $profile)
		echo '
								<option value="', $profile['id'], '"', $profile['id'] == $context['article']['permissions'] ? ' selected="selected"' : '', '>', $profile['label'], '</option>';

	echo '
							</select>
						</dd>
						<dt>
							<label for="article_styles">', $txt['sp_admin_articles_col_styles'], ':</label>
						</dt>
						<dd>
							<select name="styles" id="article_styles">';

	foreach ($context['article']['style_profiles'] as $profile)
		echo '
								<option value="', $profile['id'], '"', $profile['id'] == $context['article']['styles'] ? ' selected="selected"' : '', '>', $profile['label'], '</option>';

	echo '
							</select>
						</dd>
						<dt>
							<label for="article_status">', $txt['sp_admin_articles_col_status'], ':</label>
						</dt>
						<dd>
							<input type="checkbox" name="status" id="article_status" value="1"', $context['article']['status'] ? ' checked="checked"' : '', ' class="input_check" />
						</dd>
						<dt>
							', $txt['sp_admin_articles_col_body'], ':
						</dt>
						<dd>
						</dd>
					</dl>
					<div id="sp_rich_editor">
						<div id="sp_rich_bbc"', $context['article']['type'] != 'bbc' ? ' style="display: none;"' : '', '></div>
						<div id="sp_rich_smileys"', $context['article']['type'] != 'bbc' ? ' style="display: none;"' : '', '></div>
						<div>', template_control_richedit($context['post_box_name'], 'sp_rich_smileys', 'sp_rich_bbc'), '</div>
					</div>
					<div class="sp_button_container">
						<input type="submit" name="preview" value="', $txt['sp_admin_articles_preview'], '" class="button_submit" /> <input type="submit" name="submit" value="', $context['page_title'], '" class="button_submit" />
					</div>
				</div>
				<span class="botslice"><span></span></span>
			</div>
			<input type="hidden" name="article_id" value="', $context['article']['id'], '" />
			<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
		</form>
	</div>
	<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
		function sp_update_editor()
		{
			var new_state = document.getElementById("article_type").value;
			if (new_state == "bbc")
			{
				document.getElementById("sp_rich_bbc").style.display = "";
				document.getElementById("sp_rich_smileys").style.display = "";
			}
			else
			{
				if (oEditorHandle_content.bRichTextEnabled)
					oEditorHandle_content.toggleView();

				document.getElementById("sp_rich_bbc").style.display = "none";
				document.getElementById("sp_rich_smileys").style.display = "none";
			}
		}
	// ]]></script>';
}