class VisualEditorPage
  include PageObject

  include URL
  page_url URL.url('User:Selenium_user')

  div(:container_disabled, class: 'oo-ui-widget oo-ui-widget-disabled oo-ui-flaggableElement-constructive oo-ui-pushButtonWidget')
  div(:content, class: 've-ce-documentNode ve-ce-branchNode')
  span(:decrease_indentation, class: 'oo-ui-widget oo-ui-tool oo-ui-tool-name-outdent oo-ui-widget-disabled')
  a(:decrease_indentation_on, title: /Decrease indentation/)
  span(:downarrow, class: 'oo-ui-iconedElement-icon oo-ui-icon-down')
  a(:edit_ve, title: /Edit this page with VisualEditor/)
  span(:heading, text: 'Heading')
  span(:increase_indentation, class: 'oo-ui-widget oo-ui-tool oo-ui-tool-name-indent oo-ui-widget-disabled')
  a(:increase_indentation_on, title: /Increase indentation/)
  div(:insert_references, class: 'oo-ui-window-title')
  span(:internal_linksuggestion, text: 'Main Page')
  div(:ip_warning, class: 've-ui-mwNoticesPopupTool-item')
  span(:linksuggestion, text: 'http://www.example.com')
  span(:looks_good, class: 'oo-ui-labeledElement-label', text: 'Looks good to me')
  span(:more_menu, text: 'More')
  span(:newpage_linksuggestion, text: 'DoesNotExist')
  div(:page_text, id: 'mw-content-text')
  span(:page_title, text: 'Page title')
  span(:paragraph, text: 'Paragraph')
  span(:preformatted, text: 'Preformatted')
  span(:refs_link, text: 'Reference')
  div(:save_disabled, class: 'oo-ui-widget oo-ui-widget-disabled oo-ui-flaggableElement-constructive oo-ui-pushButtonWidget')
  span(:save_page, class: 'oo-ui-labeledElement-label', text: 'Save page')
  span(:subheading1, text: 'Sub-heading 1')
  span(:subheading2, text: 'Sub-heading 2')
  span(:subheading3, text: 'Sub-heading 3')
  span(:subheading4, text: 'Sub-heading 4')
  span(:ve_bullets, class: 'oo-ui-iconedElement-icon oo-ui-icon-bullet-list')
  span(:ve_computer_code, class: 'oo-ui-iconedElement-icon oo-ui-icon-code')
  div(:ve_heading_menu, class: 'oo-ui-iconedElement-icon oo-ui-icon-down')
  span(:ve_link_icon, class: 'oo-ui-iconedElement-icon oo-ui-icon-link')
  span(:ve_references, class: 'oo-ui-iconedElement-icon oo-ui-icon-reference')
  span(:ve_numbering, class: 'oo-ui-iconedElement-icon oo-ui-icon-number-list')
  span(:ve_strikethrough, class: 'oo-ui-iconedElement-icon oo-ui-icon-strikethrough-s')
  span(:ve_subscript, class: 'oo-ui-iconedElement-icon oo-ui-icon-subscript')
  span(:ve_superscript, class: 'oo-ui-iconedElement-icon oo-ui-icon-superscript')
  span(:ve_underline, class: 'oo-ui-iconedElement-icon oo-ui-icon-underline-u')
  div(:visual_editor_toolbar, class: 'oo-ui-toolbar-tools')
  span(:transclusion, class: 'oo-ui-iconedElement-icon oo-ui-icon-template')

  in_frame(:index => 0) do |frame|
    text_area(:describe_change, index: 0, frame: frame)
    div(:diff_view, class: 've-ui-mwSaveDialog-viewer', frame: frame)
    a(:ex, title: 'Close', frame: frame)
    span(:leftarrowclose, class: 'oo-ui-iconedElement-icon oo-ui-icon-previous', frame: frame)
    text_field(:link_textfield, index: 0, frame: frame)
    checkbox(:minor_edit, id: 'wpMinoredit', frame: frame)
    span(:return_to_save, class: 'oo-ui-labeledElement-label', text: 'Return to save form', frame: frame)
    span(:review_changes, class: 'oo-ui-labeledElement-label', text: 'Review your changes', frame: frame)
    span(:second_save_page, class: 'oo-ui-labeledElement-label', text: 'Save page', frame: frame)
    list_item(:template_list_item, text: 'S', frame: frame)
    div(:ve_link_ui, class: 'oo-ui-window-head', frame: frame)
  end

  in_frame(:index => 1) do |frame|
    a(:beta_warning, title: 'Close', frame: frame)
    div(:content_box, class: 've-ce-documentNode ve-ce-branchNode', frame: frame)
    div(:links_diff_view, class: 've-ui-mwSaveDialog-viewer', frame: frame)
    span(:links_review_changes, class: 'oo-ui-labeledElement-label', text: 'Review your changes', frame: frame)
  end

  in_frame(:index => 2) do |frame|
    span(:add_parameter, class: 've-ui-mwParameterResultWidget-name', frame: frame)
    span(:add_template, text: 'Add template', frame: frame)
    span(:apply_changes, text: 'Apply changes', frame: frame)
    div(:content_box, class: 've-ce-documentNode ve-ce-branchNode', frame: frame)
    span(:insert_reference, text: 'Insert reference', frame: frame)
    text_field(:parameter_box, index: 0, frame: frame)
    span(:remove_parameter, text: 'Remove parameter', frame: frame)
    span(:remove_template, text: 'Remove template', frame: frame)
    unordered_list(:suggestion_list, class: 'oo-ui-widget oo-ui-selectWidget oo-ui-clippableElement-clippable oo-ui-menuWidget oo-ui-textInputMenuWidget oo-ui-lookupWidget-menu ve-ui-mwTitleInputWidget-menu', frame: frame)
    div(:title, class: 'oo-ui-window-title', frame: frame)
    text_area(:transclusion_textarea, index: 0, frame: frame)
    text_field(:transclusion_textfield, index: 0, frame: frame)
  end
end
