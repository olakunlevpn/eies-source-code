"use strict";

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return exports; }; var exports = {}, Op = Object.prototype, hasOwn = Op.hasOwnProperty, defineProperty = Object.defineProperty || function (obj, key, desc) { obj[key] = desc.value; }, $Symbol = "function" == typeof Symbol ? Symbol : {}, iteratorSymbol = $Symbol.iterator || "@@iterator", asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator", toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag"; function define(obj, key, value) { return Object.defineProperty(obj, key, { value: value, enumerable: !0, configurable: !0, writable: !0 }), obj[key]; } try { define({}, ""); } catch (err) { define = function define(obj, key, value) { return obj[key] = value; }; } function wrap(innerFn, outerFn, self, tryLocsList) { var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator, generator = Object.create(protoGenerator.prototype), context = new Context(tryLocsList || []); return defineProperty(generator, "_invoke", { value: makeInvokeMethod(innerFn, self, context) }), generator; } function tryCatch(fn, obj, arg) { try { return { type: "normal", arg: fn.call(obj, arg) }; } catch (err) { return { type: "throw", arg: err }; } } exports.wrap = wrap; var ContinueSentinel = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var IteratorPrototype = {}; define(IteratorPrototype, iteratorSymbol, function () { return this; }); var getProto = Object.getPrototypeOf, NativeIteratorPrototype = getProto && getProto(getProto(values([]))); NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype); var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype); function defineIteratorMethods(prototype) { ["next", "throw", "return"].forEach(function (method) { define(prototype, method, function (arg) { return this._invoke(method, arg); }); }); } function AsyncIterator(generator, PromiseImpl) { function invoke(method, arg, resolve, reject) { var record = tryCatch(generator[method], generator, arg); if ("throw" !== record.type) { var result = record.arg, value = result.value; return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) { invoke("next", value, resolve, reject); }, function (err) { invoke("throw", err, resolve, reject); }) : PromiseImpl.resolve(value).then(function (unwrapped) { result.value = unwrapped, resolve(result); }, function (error) { return invoke("throw", error, resolve, reject); }); } reject(record.arg); } var previousPromise; defineProperty(this, "_invoke", { value: function value(method, arg) { function callInvokeWithMethodAndArg() { return new PromiseImpl(function (resolve, reject) { invoke(method, arg, resolve, reject); }); } return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(innerFn, self, context) { var state = "suspendedStart"; return function (method, arg) { if ("executing" === state) throw new Error("Generator is already running"); if ("completed" === state) { if ("throw" === method) throw arg; return doneResult(); } for (context.method = method, context.arg = arg;;) { var delegate = context.delegate; if (delegate) { var delegateResult = maybeInvokeDelegate(delegate, context); if (delegateResult) { if (delegateResult === ContinueSentinel) continue; return delegateResult; } } if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) { if ("suspendedStart" === state) throw state = "completed", context.arg; context.dispatchException(context.arg); } else "return" === context.method && context.abrupt("return", context.arg); state = "executing"; var record = tryCatch(innerFn, self, context); if ("normal" === record.type) { if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue; return { value: record.arg, done: context.done }; } "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg); } }; } function maybeInvokeDelegate(delegate, context) { var methodName = context.method, method = delegate.iterator[methodName]; if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel; var record = tryCatch(method, delegate.iterator, context.arg); if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel; var info = record.arg; return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel); } function pushTryEntry(locs) { var entry = { tryLoc: locs[0] }; 1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry); } function resetTryEntry(entry) { var record = entry.completion || {}; record.type = "normal", delete record.arg, entry.completion = record; } function Context(tryLocsList) { this.tryEntries = [{ tryLoc: "root" }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0); } function values(iterable) { if (iterable) { var iteratorMethod = iterable[iteratorSymbol]; if (iteratorMethod) return iteratorMethod.call(iterable); if ("function" == typeof iterable.next) return iterable; if (!isNaN(iterable.length)) { var i = -1, next = function next() { for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next; return next.value = undefined, next.done = !0, next; }; return next.next = next; } } return { next: doneResult }; } function doneResult() { return { value: undefined, done: !0 }; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), defineProperty(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) { var ctor = "function" == typeof genFun && genFun.constructor; return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name)); }, exports.mark = function (genFun) { return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun; }, exports.awrap = function (arg) { return { __await: arg }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () { return this; }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) { void 0 === PromiseImpl && (PromiseImpl = Promise); var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl); return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) { return result.done ? result.value : iter.next(); }); }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () { return this; }), define(Gp, "toString", function () { return "[object Generator]"; }), exports.keys = function (val) { var object = Object(val), keys = []; for (var key in object) keys.push(key); return keys.reverse(), function next() { for (; keys.length;) { var key = keys.pop(); if (key in object) return next.value = key, next.done = !1, next; } return next.done = !0, next; }; }, exports.values = values, Context.prototype = { constructor: Context, reset: function reset(skipTempReset) { if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined); }, stop: function stop() { this.done = !0; var rootRecord = this.tryEntries[0].completion; if ("throw" === rootRecord.type) throw rootRecord.arg; return this.rval; }, dispatchException: function dispatchException(exception) { if (this.done) throw exception; var context = this; function handle(loc, caught) { return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught; } for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i], record = entry.completion; if ("root" === entry.tryLoc) return handle("end"); if (entry.tryLoc <= this.prev) { var hasCatch = hasOwn.call(entry, "catchLoc"), hasFinally = hasOwn.call(entry, "finallyLoc"); if (hasCatch && hasFinally) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } else if (hasCatch) { if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0); } else { if (!hasFinally) throw new Error("try statement without catch or finally"); if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc); } } } }, abrupt: function abrupt(type, arg) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) { var finallyEntry = entry; break; } } finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null); var record = finallyEntry ? finallyEntry.completion : {}; return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record); }, complete: function complete(record, afterLoc) { if ("throw" === record.type) throw record.arg; return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel; }, finish: function finish(finallyLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel; } }, "catch": function _catch(tryLoc) { for (var i = this.tryEntries.length - 1; i >= 0; --i) { var entry = this.tryEntries[i]; if (entry.tryLoc === tryLoc) { var record = entry.completion; if ("throw" === record.type) { var thrown = record.arg; resetTryEntry(entry); } return thrown; } } throw new Error("illegal catch attempt"); }, delegateYield: function delegateYield(iterable, resultName, nextLoc) { return this.delegate = { iterator: values(iterable), resultName: resultName, nextLoc: nextLoc }, "next" === this.method && (this.arg = undefined), ContinueSentinel; } }, exports; }
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter); }
function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }
stm_lms_components['user_data_transfer'] = {
  template: '#stm-lms-dashboard-user_data_transfer',
  props: ['course_id'],
  data: function data() {
    return {
      modalVisible: false,
      fileTypeError: false,
      isCSVExporting: false,
      emptyCsvFile: false,
      userDataFileName: '',
      importFileSize: '',
      userData: [],
      totalUsers: 0,
      importedUsers: 0,
      importProgress: 0,
      importStep: 0,
      newEnrolledUsers: [],
      beforeEnrolledUsers: [],
      notEnrolledUsers: [],
      incorrectEmailUsers: [],
      afterImport: false
    };
  },
  mounted: function mounted() {
    var $this = this;
    document.addEventListener('click', this.clickOutsideModal);
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (eventName) {
      $this.$refs.uploadFileDropArea.addEventListener(eventName, function (e) {
        e.preventDefault();
        e.stopPropagation();
      }, false);
    });
    ['dragenter', 'dragover'].forEach(function (eventName) {
      $this.$refs.uploadFileDropArea.addEventListener(eventName, function (e) {
        e.target.classList.add('highlight');
      }, false);
    });
    ['dragleave', 'drop'].forEach(function (eventName) {
      $this.$refs.uploadFileDropArea.addEventListener(eventName, function (e) {
        e.target.classList.remove('highlight');
      }, false);
    });
    $this.$refs.uploadFileDropArea.addEventListener('drop', function (e) {
      $this.fileUploadHandler(e.dataTransfer.files);
      $this.$forceUpdate();
    }, false);
  },
  methods: {
    uploadImportFile: function uploadImportFile() {
      var $this = this;
      $this.importStep = 0;
      $this.$refs.importFileInput.click();
      $this.$refs.importFileInput.addEventListener('change', function (event) {
        $this.fileUploadHandler(event.target.files);
      });
    },
    fileUploadHandler: function fileUploadHandler(files) {
      var $this = this;
      $this.deleteAttachedFile();
      if (files === undefined || files === null) return;
      $this.$refs.importFileInput.files = files;
      var file = files[0];
      if (file === undefined || file === null) return;
      var fileExtension = file.name.split('.').pop().toLowerCase();
      if (!['csv'].includes(fileExtension)) {
        $this.fileTypeError = true;
        return;
      }
      $this.userDataFileName = file.name;
      $this.importFileSize = $this.setFileSize(file.size);
      $this.readCSVFile(file).then(function (data) {
        var headers = ['email'];
        var csvHeaders = Object.keys(data[0] || []);
        var isValidCSV = headers.every(function (header) {
          return csvHeaders.includes(header);
        });
        if (isValidCSV && csvHeaders.length) {
          $this.totalUsers = data.length;
          $this.userData = data;
          $this.importStep = 1;
        } else {
          $this.emptyCsvFile = true;
        }
      })["catch"](function (error) {
        console.error('Error reading CSV file:', error);
      });
    },
    closeImportModal: function closeImportModal() {
      this.userDataFileName = '';
      this.importStep = 0;
      this.userData = [];
      this.modalVisible = false;
      this.emptyCsvFile = false;
      this.fileTypeError = false;
      if (this.afterImport) {
        this.$emit('studentAdded');
        this.afterImport = false;
      }
    },
    clickOutsideModal: function clickOutsideModal(event) {
      if (event.target === this.$refs.transferModal) {
        this.userDataFileName = '';
        this.importStep = 0;
        this.userData = [];
        this.modalVisible = false;
        if (this.afterImport) {
          this.$emit('studentAdded');
          this.afterImport = false;
        }
      }
    },
    deleteAttachedFile: function deleteAttachedFile() {
      this.userDataFileName = '';
      this.importStep = 0;
      this.userData = [];
      this.emptyCsvFile = false;
      this.fileTypeError = false;
    },
    importUsers: function () {
      var _importUsers = _asyncToGenerator( /*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
        var $this, endpoint, batchSize, total, processed, i, _$data$new_enrolled_u, _$data$before_enrolle, _$data$incorrect_emai, _$data$not_enrolled_u, batch, response, $data, _$this$newEnrolledUse, _$this$beforeEnrolled, _$this$incorrectEmail, _$this$notEnrolledUse, beforeCount, enrolledCount, incorrectCount, notEnrolledCount;
        return _regeneratorRuntime().wrap(function _callee$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              $this = this;
              $this.importStep = 2;
              $this.beforeEnrolledUsers = [];
              $this.newEnrolledUsers = [];
              $this.incorrectEmailUsers = [];
              $this.notEnrolledUsers = [];
              $this.importProgress = 0;
              $this.importedUsers = 0;
              endpoint = "".concat(stm_lms_ajaxurl, "?action=stm_lms_dashboard_import_users_to_course&nonce=").concat(stm_lms_nonces['stm_lms_dashboard_import_users_to_course']);
              batchSize = 25;
              total = $this.userData.length;
              processed = 0;
              _context.prev = 12;
              i = 0;
            case 14:
              if (!(i < total)) {
                _context.next = 32;
                break;
              }
              batch = $this.userData.slice(i, i + batchSize);
              _context.next = 18;
              return $this.$http.post(endpoint, {
                users: batch,
                course_id: $this.course_id
              });
            case 18:
              response = _context.sent;
              $data = response.body && response.body.data ? response.body.data : response.body;
              if ((_$data$new_enrolled_u = $data.new_enrolled_users) !== null && _$data$new_enrolled_u !== void 0 && _$data$new_enrolled_u.length) {
                (_$this$newEnrolledUse = $this.newEnrolledUsers).push.apply(_$this$newEnrolledUse, _toConsumableArray($data.new_enrolled_users));
              }
              if ((_$data$before_enrolle = $data.before_enrolled_users) !== null && _$data$before_enrolle !== void 0 && _$data$before_enrolle.length) {
                (_$this$beforeEnrolled = $this.beforeEnrolledUsers).push.apply(_$this$beforeEnrolled, _toConsumableArray($data.before_enrolled_users));
              }
              if ((_$data$incorrect_emai = $data.incorrect_email_users) !== null && _$data$incorrect_emai !== void 0 && _$data$incorrect_emai.length) {
                (_$this$incorrectEmail = $this.incorrectEmailUsers).push.apply(_$this$incorrectEmail, _toConsumableArray($data.incorrect_email_users));
              }
              if ((_$data$not_enrolled_u = $data.not_enrolled_users) !== null && _$data$not_enrolled_u !== void 0 && _$data$not_enrolled_u.length) {
                (_$this$notEnrolledUse = $this.notEnrolledUsers).push.apply(_$this$notEnrolledUse, _toConsumableArray($data.not_enrolled_users));
              }
              processed += batch.length;
              $this.importedUsers = $this.newEnrolledUsers.length;
              $this.importProgress = Math.round(processed / total * 100);
              _context.next = 29;
              return new Promise(function (r) {
                return setTimeout(r, 120);
              });
            case 29:
              i += batchSize;
              _context.next = 14;
              break;
            case 32:
              beforeCount = $this.beforeEnrolledUsers.length;
              enrolledCount = $this.newEnrolledUsers.length;
              incorrectCount = $this.incorrectEmailUsers.length;
              notEnrolledCount = $this.notEnrolledUsers.length;
              if (beforeCount === 0) {
                setTimeout(function () {
                  $this.importStep = 3;
                }, 600);
              } else {
                setTimeout(function () {
                  $this.importStep = 4;
                }, 600);
              }
              if ((incorrectCount > 0 || notEnrolledCount > 0) && beforeCount === 0 && enrolledCount === 0) {
                setTimeout(function () {
                  $this.importStep = 5;
                }, 600);
              }
              $this.afterImport = true;
              _context.next = 49;
              break;
            case 41:
              _context.prev = 41;
              _context.t0 = _context["catch"](12);
              console.error('Import error:', _context.t0);
              $this.importStep = 5;
              $this.userDataFileName = '';
              $this.userData = [];
              $this.emptyCsvFile = false;
              $this.fileTypeError = false;
            case 49:
            case "end":
              return _context.stop();
          }
        }, _callee, this, [[12, 41]]);
      }));
      function importUsers() {
        return _importUsers.apply(this, arguments);
      }
      return importUsers;
    }(),
    exportUsers: function exportUsers() {
      var $this = this;
      $this.isCSVExporting = true;
      $this.$http.post("".concat(stm_lms_ajaxurl, "?action=stm_lms_dashboard_export_course_students&nonce=").concat(stm_lms_nonces['stm_lms_dashboard_export_course_students_to_csv']), {
        course_id: $this.course_id
      }).then(function (response) {
        if (null !== response.body.user_data && response.body.user_data.length) {
          $this.downloadCSV({
            filename: response.body.filename
          }, response.body.user_data);
        }
        setTimeout(function () {
          $this.isCSVExporting = false;
        }, 1500);
      });
    },
    readCSVFile: function readCSVFile(file) {
      return new Promise(function (resolve, reject) {
        var reader = new FileReader();
        reader.readAsText(file, 'UTF-8');
        reader.onload = function (event) {
          var csvData = event.target.result;
          var lines = csvData.split('\n');
          var headers = lines[0].trim().split(',').map(function (header) {
            return header.trim();
          });
          var dataArray = [];
          for (var i = 1; i < lines.length; i++) {
            var values = lines[i].trim().split(',').map(function (value) {
              return value.trim();
            });
            var obj = {};
            for (var j = 0; j < headers.length; j++) {
              var key = headers[j];
              if (values[j] !== undefined && values[j] !== '') {
                obj[key] = values[j];
              }
            }
            if (Object.keys(obj).length > 0) {
              dataArray.push(obj);
            }
          }
          resolve(dataArray);
        };
        reader.onerror = function (error) {
          reject(error);
        };
      });
    },
    downloadCSV: function downloadCSV(args, stockData) {
      var data,
        filename,
        link,
        csvUtf = '';
      var csv = this.convertArrayofObjectsToCSV({
        data: stockData
      });
      if (csv == null) return;
      filename = args.filename || 'students.csv';
      if (!csv.match(/^data:text\/csv/i)) {
        csvUtf = 'data:text/csv;charset=utf-8,';
      }
      data = encodeURI(csvUtf) + "\uFEFF" + encodeURI(csv);
      link = document.createElement('a');
      link.setAttribute('href', data);
      link.setAttribute('download', filename);
      link.click();
    },
    convertArrayofObjectsToCSV: function convertArrayofObjectsToCSV(args) {
      var result, keys, columnDelimeter, lineDelimeter, data;
      data = args.data || null;
      if (data == null || !data.length) {
        return null;
      }
      columnDelimeter = args.columnDelimeter || ',';
      lineDelimeter = args.lineDelimeter || '\r\n';
      keys = Object.keys(data[0]);
      result = '';
      result += keys.join(columnDelimeter);
      result += lineDelimeter;
      data.forEach(function (item) {
        keys.forEach(function (key, index) {
          if (index > 0) result += columnDelimeter + ' ';
          result += item[key] || '';
        });
        result += lineDelimeter;
      });
      return result;
    },
    setFileSize: function setFileSize(bytes) {
      var KB = 1024;
      var MB = KB * 1024;
      var GB = MB * 1024;
      if (bytes >= GB) {
        return (bytes / GB).toFixed(2) + ' gb';
      } else if (bytes >= MB) {
        return (bytes / MB).toFixed(2) + ' mb';
      } else if (bytes >= KB || bytes / KB < 1) {
        return (bytes / KB).toFixed(2) + ' kb';
      } else {
        return bytes + ' bytes';
      }
    }
  }
};