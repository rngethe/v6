UPDATE `jos_fabrik_elements` JOIN `jos_fabrik_groups` ON `jos_fabrik_elements`.`group_id`=`jos_fabrik_groups`.`id` SET `jos_fabrik_elements`.`params` = '{\"placeholder\":\"\",\"password\":\"0\",\"maxlength\":\"255\",\"disable\":\"0\",\"readonly\":\"0\",\"autocomplete\":\"1\",\"speech\":\"0\",\"advanced_behavior\":\"0\",\"bootstrap_class\":\"input-medium\",\"text_format\":\"text\",\"integer_length\":\"6\",\"decimal_length\":\"2\",\"field_use_number_format\":\"0\",\"field_thousand_sep\":\",\",\"field_decimal_sep\":\".\",\"text_format_string\":\"\",\"field_format_string_blank\":\"1\",\"text_input_mask\":\"\",\"text_input_mask_autoclear\":\"0\",\"text_input_mask_definitions\":\"\",\"render_as_qrcode\":\"0\",\"scan_qrcode\":\"0\",\"guess_linktype\":\"0\",\"link_target_options\":\"default\",\"rel\":\"\",\"link_title\":\"\",\"link_attributes\":\"\",\"show_in_rss_feed\":\"0\",\"show_label_in_rss_feed\":\"0\",\"use_as_rss_enclosure\":\"0\",\"rollover\":\"\",\"tipseval\":\"0\",\"tiplocation\":\"top-left\",\"labelindetails\":\"0\",\"labelinlist\":\"0\",\"comment\":\"\",\"edit_access\":\"1\",\"edit_access_user\":\"\",\"view_access\":\"1\",\"view_access_user\":\"\",\"list_view_access\":\"1\",\"encrypt\":\"0\",\"store_in_db\":\"1\",\"default_on_copy\":\"0\",\"can_order\":\"1\",\"alt_list_heading\":\"\",\"custom_link\":\"\",\"custom_link_target\":\"\",\"custom_link_indetails\":\"1\",\"use_as_row_class\":\"0\",\"include_in_list_query\":\"1\",\"always_render\":\"0\",\"icon_hovertext\":\"1\",\"icon_file\":\"\",\"icon_subdir\":\"\",\"filter_length\":\"20\",\"filter_access\":\"1\",\"full_words_only\":\"0\",\"filter_required\":\"0\",\"filter_build_method\":\"0\",\"filter_groupby\":\"text\",\"inc_in_adv_search\":\"1\",\"filter_class\":\"input-medium\",\"filter_responsive_class\":\"\",\"tablecss_header_class\":\"\",\"tablecss_header\":\"\",\"tablecss_cell_class\":\"\",\"tablecss_cell\":\"\",\"sum_on\":\"0\",\"sum_label\":\"Sum\",\"sum_access\":\"8\",\"sum_split\":\"\",\"avg_on\":\"0\",\"avg_label\":\"Average\",\"avg_access\":\"8\",\"avg_round\":\"0\",\"avg_split\":\"\",\"median_on\":\"0\",\"median_label\":\"Median\",\"median_access\":\"8\",\"median_split\":\"\",\"count_on\":\"0\",\"count_label\":\"Count\",\"count_condition\":\"\",\"count_access\":\"8\",\"count_split\":\"\",\"custom_calc_on\":\"0\",\"custom_calc_label\":\"Custom\",\"custom_calc_query\":\"\",\"custom_calc_access\":\"1\",\"custom_calc_split\":\"\",\"custom_calc_php\":\"\",\"validations\":[]}' WHERE `jos_fabrik_groups`.`name`='jos_emundus_stats_files_graph' OR `jos_fabrik_groups`.`name`='jos_emundus_stats_nombre_comptes' OR `jos_fabrik_groups`.`name`='NEW_CANDIDATURE_DAY' OR `jos_fabrik_groups`.`name`='CANDIDATURE_SUBMITTED_DAY';