(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);throw new Error("Cannot find module '"+o+"'")}var f=n[o]={exports:{}};t[o][0].call(f.exports,function(e){var n=t[o][1][e];return s(n?n:e)},f,f.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
"use strict";

document.addEventListener("DOMContentLoaded", function (event) {
  new Vue({
    /**
     * @var stm_import_data
     */
    el: '#stm-elementor-importer',
    data: {
      opened: false,
      loading: false,
      templates: []
    },
    created: function created() {
      this.fetchTemplates();
    },
    methods: {
      openImport: function openImport() {
        this.opened = true;
      },
      closeImport: function closeImport() {
        this.opened = false;
      },
      fetchTemplates: function fetchTemplates() {
        var _this = this;

        _this.loading = true;

        _this.$http.get("".concat(stm_import_data.url, "?action=stm_fetch_templates")).then(function (data) {
          _this.loading = false;

          _this.$set(_this, 'templates', data.body.library.templates);
        });
      },
      importTemplate: function importTemplate(id) {
        var _this = this;

        _this.loading = true;
        var url = "".concat(stm_import_data.url, "?action=stm_import_template&post_id=").concat(stm_import_data.post_id, "&template_id=").concat(id);

        _this.$http.get(url).then(function () {
          /*Clear Elementor Cache*/
          _this.$http.post("".concat(stm_import_data.url), {
            action: 'elementor_clear_cache',
            _nonce: stm_import_data.nonces['clear_cache']
          }, {
            emulateJSON: true
          }).then(function () {
            location.reload();
          });
        });
      }
    }
  });
});
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImZha2VfOTgwMTI4MjEuanMiXSwibmFtZXMiOlsiZG9jdW1lbnQiLCJhZGRFdmVudExpc3RlbmVyIiwiZXZlbnQiLCJWdWUiLCJlbCIsImRhdGEiLCJvcGVuZWQiLCJsb2FkaW5nIiwidGVtcGxhdGVzIiwiY3JlYXRlZCIsImZldGNoVGVtcGxhdGVzIiwibWV0aG9kcyIsIm9wZW5JbXBvcnQiLCJjbG9zZUltcG9ydCIsIl90aGlzIiwiJGh0dHAiLCJnZXQiLCJjb25jYXQiLCJzdG1faW1wb3J0X2RhdGEiLCJ1cmwiLCJ0aGVuIiwiJHNldCIsImJvZHkiLCJsaWJyYXJ5IiwiaW1wb3J0VGVtcGxhdGUiLCJpZCIsInBvc3RfaWQiLCJwb3N0IiwiYWN0aW9uIiwiX25vbmNlIiwibm9uY2VzIiwiZW11bGF0ZUpTT04iLCJsb2NhdGlvbiIsInJlbG9hZCJdLCJtYXBwaW5ncyI6IkFBQUE7O0FBRUFBLFFBQVEsQ0FBQ0MsZ0JBQVQsQ0FBMEIsa0JBQTFCLEVBQThDLFVBQVVDLEtBQVYsRUFBaUI7QUFDN0QsTUFBSUMsR0FBSixDQUFRO0FBQ047OztBQUdBQyxJQUFBQSxFQUFFLEVBQUUseUJBSkU7QUFLTkMsSUFBQUEsSUFBSSxFQUFFO0FBQ0pDLE1BQUFBLE1BQU0sRUFBRSxLQURKO0FBRUpDLE1BQUFBLE9BQU8sRUFBRSxLQUZMO0FBR0pDLE1BQUFBLFNBQVMsRUFBRTtBQUhQLEtBTEE7QUFVTkMsSUFBQUEsT0FBTyxFQUFFLFNBQVNBLE9BQVQsR0FBbUI7QUFDMUIsV0FBS0MsY0FBTDtBQUNELEtBWks7QUFhTkMsSUFBQUEsT0FBTyxFQUFFO0FBQ1BDLE1BQUFBLFVBQVUsRUFBRSxTQUFTQSxVQUFULEdBQXNCO0FBQ2hDLGFBQUtOLE1BQUwsR0FBYyxJQUFkO0FBQ0QsT0FITTtBQUlQTyxNQUFBQSxXQUFXLEVBQUUsU0FBU0EsV0FBVCxHQUF1QjtBQUNsQyxhQUFLUCxNQUFMLEdBQWMsS0FBZDtBQUNELE9BTk07QUFPUEksTUFBQUEsY0FBYyxFQUFFLFNBQVNBLGNBQVQsR0FBMEI7QUFDeEMsWUFBSUksS0FBSyxHQUFHLElBQVo7O0FBRUFBLFFBQUFBLEtBQUssQ0FBQ1AsT0FBTixHQUFnQixJQUFoQjs7QUFFQU8sUUFBQUEsS0FBSyxDQUFDQyxLQUFOLENBQVlDLEdBQVosQ0FBZ0IsR0FBR0MsTUFBSCxDQUFVQyxlQUFlLENBQUNDLEdBQTFCLEVBQStCLDZCQUEvQixDQUFoQixFQUErRUMsSUFBL0UsQ0FBb0YsVUFBVWYsSUFBVixFQUFnQjtBQUNsR1MsVUFBQUEsS0FBSyxDQUFDUCxPQUFOLEdBQWdCLEtBQWhCOztBQUVBTyxVQUFBQSxLQUFLLENBQUNPLElBQU4sQ0FBV1AsS0FBWCxFQUFrQixXQUFsQixFQUErQlQsSUFBSSxDQUFDaUIsSUFBTCxDQUFVQyxPQUFWLENBQWtCZixTQUFqRDtBQUNELFNBSkQ7QUFLRCxPQWpCTTtBQWtCUGdCLE1BQUFBLGNBQWMsRUFBRSxTQUFTQSxjQUFULENBQXdCQyxFQUF4QixFQUE0QjtBQUMxQyxZQUFJWCxLQUFLLEdBQUcsSUFBWjs7QUFFQUEsUUFBQUEsS0FBSyxDQUFDUCxPQUFOLEdBQWdCLElBQWhCO0FBQ0EsWUFBSVksR0FBRyxHQUFHLEdBQUdGLE1BQUgsQ0FBVUMsZUFBZSxDQUFDQyxHQUExQixFQUErQixzQ0FBL0IsRUFBdUVGLE1BQXZFLENBQThFQyxlQUFlLENBQUNRLE9BQTlGLEVBQXVHLGVBQXZHLEVBQXdIVCxNQUF4SCxDQUErSFEsRUFBL0gsQ0FBVjs7QUFFQVgsUUFBQUEsS0FBSyxDQUFDQyxLQUFOLENBQVlDLEdBQVosQ0FBZ0JHLEdBQWhCLEVBQXFCQyxJQUFyQixDQUEwQixZQUFZO0FBQ3BDO0FBQ0FOLFVBQUFBLEtBQUssQ0FBQ0MsS0FBTixDQUFZWSxJQUFaLENBQWlCLEdBQUdWLE1BQUgsQ0FBVUMsZUFBZSxDQUFDQyxHQUExQixDQUFqQixFQUFpRDtBQUMvQ1MsWUFBQUEsTUFBTSxFQUFFLHVCQUR1QztBQUUvQ0MsWUFBQUEsTUFBTSxFQUFFWCxlQUFlLENBQUNZLE1BQWhCLENBQXVCLGFBQXZCO0FBRnVDLFdBQWpELEVBR0c7QUFDREMsWUFBQUEsV0FBVyxFQUFFO0FBRFosV0FISCxFQUtHWCxJQUxILENBS1EsWUFBWTtBQUNsQlksWUFBQUEsUUFBUSxDQUFDQyxNQUFUO0FBQ0QsV0FQRDtBQVFELFNBVkQ7QUFXRDtBQW5DTTtBQWJILEdBQVI7QUFtREQsQ0FwREQiLCJzb3VyY2VzQ29udGVudCI6WyJcInVzZSBzdHJpY3RcIjtcblxuZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcihcIkRPTUNvbnRlbnRMb2FkZWRcIiwgZnVuY3Rpb24gKGV2ZW50KSB7XG4gIG5ldyBWdWUoe1xuICAgIC8qKlxuICAgICAqIEB2YXIgc3RtX2ltcG9ydF9kYXRhXG4gICAgICovXG4gICAgZWw6ICcjc3RtLWVsZW1lbnRvci1pbXBvcnRlcicsXG4gICAgZGF0YToge1xuICAgICAgb3BlbmVkOiBmYWxzZSxcbiAgICAgIGxvYWRpbmc6IGZhbHNlLFxuICAgICAgdGVtcGxhdGVzOiBbXVxuICAgIH0sXG4gICAgY3JlYXRlZDogZnVuY3Rpb24gY3JlYXRlZCgpIHtcbiAgICAgIHRoaXMuZmV0Y2hUZW1wbGF0ZXMoKTtcbiAgICB9LFxuICAgIG1ldGhvZHM6IHtcbiAgICAgIG9wZW5JbXBvcnQ6IGZ1bmN0aW9uIG9wZW5JbXBvcnQoKSB7XG4gICAgICAgIHRoaXMub3BlbmVkID0gdHJ1ZTtcbiAgICAgIH0sXG4gICAgICBjbG9zZUltcG9ydDogZnVuY3Rpb24gY2xvc2VJbXBvcnQoKSB7XG4gICAgICAgIHRoaXMub3BlbmVkID0gZmFsc2U7XG4gICAgICB9LFxuICAgICAgZmV0Y2hUZW1wbGF0ZXM6IGZ1bmN0aW9uIGZldGNoVGVtcGxhdGVzKCkge1xuICAgICAgICB2YXIgX3RoaXMgPSB0aGlzO1xuXG4gICAgICAgIF90aGlzLmxvYWRpbmcgPSB0cnVlO1xuXG4gICAgICAgIF90aGlzLiRodHRwLmdldChcIlwiLmNvbmNhdChzdG1faW1wb3J0X2RhdGEudXJsLCBcIj9hY3Rpb249c3RtX2ZldGNoX3RlbXBsYXRlc1wiKSkudGhlbihmdW5jdGlvbiAoZGF0YSkge1xuICAgICAgICAgIF90aGlzLmxvYWRpbmcgPSBmYWxzZTtcblxuICAgICAgICAgIF90aGlzLiRzZXQoX3RoaXMsICd0ZW1wbGF0ZXMnLCBkYXRhLmJvZHkubGlicmFyeS50ZW1wbGF0ZXMpO1xuICAgICAgICB9KTtcbiAgICAgIH0sXG4gICAgICBpbXBvcnRUZW1wbGF0ZTogZnVuY3Rpb24gaW1wb3J0VGVtcGxhdGUoaWQpIHtcbiAgICAgICAgdmFyIF90aGlzID0gdGhpcztcblxuICAgICAgICBfdGhpcy5sb2FkaW5nID0gdHJ1ZTtcbiAgICAgICAgdmFyIHVybCA9IFwiXCIuY29uY2F0KHN0bV9pbXBvcnRfZGF0YS51cmwsIFwiP2FjdGlvbj1zdG1faW1wb3J0X3RlbXBsYXRlJnBvc3RfaWQ9XCIpLmNvbmNhdChzdG1faW1wb3J0X2RhdGEucG9zdF9pZCwgXCImdGVtcGxhdGVfaWQ9XCIpLmNvbmNhdChpZCk7XG5cbiAgICAgICAgX3RoaXMuJGh0dHAuZ2V0KHVybCkudGhlbihmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgLypDbGVhciBFbGVtZW50b3IgQ2FjaGUqL1xuICAgICAgICAgIF90aGlzLiRodHRwLnBvc3QoXCJcIi5jb25jYXQoc3RtX2ltcG9ydF9kYXRhLnVybCksIHtcbiAgICAgICAgICAgIGFjdGlvbjogJ2VsZW1lbnRvcl9jbGVhcl9jYWNoZScsXG4gICAgICAgICAgICBfbm9uY2U6IHN0bV9pbXBvcnRfZGF0YS5ub25jZXNbJ2NsZWFyX2NhY2hlJ11cbiAgICAgICAgICB9LCB7XG4gICAgICAgICAgICBlbXVsYXRlSlNPTjogdHJ1ZVxuICAgICAgICAgIH0pLnRoZW4oZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgbG9jYXRpb24ucmVsb2FkKCk7XG4gICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuICAgICAgfVxuICAgIH1cbiAgfSk7XG59KTsiXX0=
},{}]},{},[1])