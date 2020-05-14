ALTER TABLE `jos_emundus_setup_letters` ADD `pdf` INT(1) NOT NULL DEFAULT '0' AFTER `file`; 

INSERT INTO `jos_fabrik_elements` (`name`, `group_id`, `plugin`, `label`, `checked_out`, `checked_out_time`, `created`, `created_by`, `created_by_alias`, `modified`, `modified_by`, `width`, `height`, `default`, `hidden`, `eval`, `ordering`, `show_in_list_summary`, `filter_type`, `filter_exact_match`, `published`, `link_to_detail`, `primary_key`, `auto_increment`, `access`, `use_in_page_title`, `parent_id`, `params`) VALUES
('pdf', 185, 'yesno', 'LETTER_EXPORT_TO_PDF', 0, '0000-00-00 00:00:00', '2020-05-14 10:14:03', 62, 'sysadmin', '0000-00-00 00:00:00', 0, 0, 0, '0', 0, 0, 2, 0, '', 1, 1, 0, 0, 0, 1, 0, 0, '{\"yesno_default\":\"0\",\"yesno_icon_yes\":\"\",\"yesno_icon_no\":\"\",\"options_per_row\":\"4\",\"toggle_others\":\"0\",\"toggle_where\":\"\",\"show_in_rss_feed\":\"0\",\"show_label_in_rss_feed\":\"0\",\"use_as_rss_enclosure\":\"0\",\"rollover\":\"\",\"tipseval\":\"0\",\"tiplocation\":\"top-left\",\"labelindetails\":\"0\",\"labelinlist\":\"0\",\"comment\":\"\",\"edit_access\":\"1\",\"edit_access_user\":\"\",\"view_access\":\"1\",\"view_access_user\":\"\",\"list_view_access\":\"1\",\"encrypt\":\"0\",\"store_in_db\":\"1\",\"default_on_copy\":\"0\",\"can_order\":\"0\",\"alt_list_heading\":\"\",\"custom_link\":\"\",\"custom_link_target\":\"\",\"custom_link_indetails\":\"1\",\"use_as_row_class\":\"0\",\"include_in_list_query\":\"1\",\"always_render\":\"0\",\"icon_folder\":\"0\",\"icon_hovertext\":\"1\",\"icon_file\":\"\",\"icon_subdir\":\"\",\"filter_length\":\"20\",\"filter_access\":\"1\",\"full_words_only\":\"0\",\"filter_required\":\"0\",\"filter_build_method\":\"0\",\"filter_groupby\":\"text\",\"inc_in_adv_search\":\"1\",\"filter_class\":\"input-medium\",\"filter_responsive_class\":\"\",\"tablecss_header_class\":\"\",\"tablecss_header\":\"\",\"tablecss_cell_class\":\"\",\"tablecss_cell\":\"\",\"sum_on\":\"0\",\"sum_label\":\"Sum\",\"sum_access\":\"1\",\"sum_split\":\"\",\"avg_on\":\"0\",\"avg_label\":\"Average\",\"avg_access\":\"1\",\"avg_round\":\"0\",\"avg_split\":\"\",\"median_on\":\"0\",\"median_label\":\"Median\",\"median_access\":\"1\",\"median_split\":\"\",\"count_on\":\"0\",\"count_label\":\"Count\",\"count_condition\":\"\",\"count_access\":\"1\",\"count_split\":\"\",\"custom_calc_on\":\"0\",\"custom_calc_label\":\"Custom\",\"custom_calc_query\":\"\",\"custom_calc_access\":\"1\",\"custom_calc_split\":\"\",\"custom_calc_php\":\"\",\"validations\":[]}');