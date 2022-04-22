import $ from "jquery"
import Tagify from '@yaireo/tagify'

$(document).ready(function () {
  const inputTags = document.querySelector('#form-input-tags');
  var tagify = new Tagify(inputTags, {
    originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
  })
})
