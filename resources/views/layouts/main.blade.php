<!doctype html>

<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    
    @include('partials.style')
    
  </head>
  <body class="layout-fluid">
    
    <div class="page">

        @include('partials.sidebar')
     
        <div class="page-wrapper">

            @include('partials.header')
            
            <!-- BEGIN PAGE BODY -->
            <div class="page-body">
                <div class="container-xl">
                    <div class="row row-deck row-cards">
                        @yield('content')
                    </div>
                </div>
            </div>

            <!-- END PAGE BODY -->
            {{-- <!--  BEGIN FOOTER  -->
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-lg-auto ms-lg-auto">
                        <ul class="list-inline list-inline-dots mb-0">
                        <li class="list-inline-item"><a href="https://docs.tabler.io" target="_blank" class="link-secondary" rel="noopener">Documentation</a></li>
                        <li class="list-inline-item"><a href="./license.html" class="link-secondary">License</a></li>
                        <li class="list-inline-item">
                            <a href="https://github.com/tabler/tabler" target="_blank" class="link-secondary" rel="noopener">Source code</a>
                        </li>
                        <li class="list-inline-item">
                            <a href="https://github.com/sponsors/codecalm" target="_blank" class="link-secondary" rel="noopener">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/heart -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon text-pink icon-inline icon-4"
                            >
                                <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                            </svg>
                            Sponsor
                            </a>
                        </li>
                        </ul>
                    </div>
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <ul class="list-inline list-inline-dots mb-0">
                        <li class="list-inline-item">
                            Copyright &copy; 2025
                            <a href="." class="link-secondary">Tabler</a>. All rights reserved.
                        </li>
                        <li class="list-inline-item">
                            <a href="./changelog.html" class="link-secondary" rel="noopener"> v1.2.0 </a>
                        </li>
                        </ul>
                    </div>
                    </div>
                </div>
            </footer>
            <!--  END FOOTER  --> --}}
        </div>
    </div>
    <!-- BEGIN PAGE MODALS -->
    <div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">New report</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" class="form-control" name="example-text-input" placeholder="Your report name" />
            </div>
            <label class="form-label">Report type</label>
            <div class="form-selectgroup-boxes row mb-3">
              <div class="col-lg-6">
                <label class="form-selectgroup-item">
                  <input type="radio" name="report-type" value="1" class="form-selectgroup-input" checked />
                  <span class="form-selectgroup-label d-flex align-items-center p-3">
                    <span class="me-3">
                      <span class="form-selectgroup-check"></span>
                    </span>
                    <span class="form-selectgroup-label-content">
                      <span class="form-selectgroup-title strong mb-1">Simple</span>
                      <span class="d-block text-secondary">Provide only basic data needed for the report</span>
                    </span>
                  </span>
                </label>
              </div>
              <div class="col-lg-6">
                <label class="form-selectgroup-item">
                  <input type="radio" name="report-type" value="1" class="form-selectgroup-input" />
                  <span class="form-selectgroup-label d-flex align-items-center p-3">
                    <span class="me-3">
                      <span class="form-selectgroup-check"></span>
                    </span>
                    <span class="form-selectgroup-label-content">
                      <span class="form-selectgroup-title strong mb-1">Advanced</span>
                      <span class="d-block text-secondary">Insert charts and additional advanced analyses to be inserted in the report</span>
                    </span>
                  </span>
                </label>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-8">
                <div class="mb-3">
                  <label class="form-label">Report url</label>
                  <div class="input-group input-group-flat">
                    <span class="input-group-text"> https://tabler.io/reports/ </span>
                    <input type="text" class="form-control ps-0" value="report-01" autocomplete="off" />
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="mb-3">
                  <label class="form-label">Visibility</label>
                  <select class="form-select">
                    <option value="1" selected>Private</option>
                    <option value="2">Public</option>
                    <option value="3">Hidden</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-6">
                <div class="mb-3">
                  <label class="form-label">Client name</label>
                  <input type="text" class="form-control" />
                </div>
              </div>
              <div class="col-lg-6">
                <div class="mb-3">
                  <label class="form-label">Reporting period</label>
                  <input type="date" class="form-control" />
                </div>
              </div>
              <div class="col-lg-12">
                <div>
                  <label class="form-label">Additional information</label>
                  <textarea class="form-control" rows="3"></textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <a href="#" class="btn btn-link link-secondary btn-3" data-bs-dismiss="modal"> Cancel </a>
            <a href="#" class="btn btn-primary btn-5 ms-auto" data-bs-dismiss="modal">
              <!-- Download SVG icon from http://tabler.io/icons/icon/plus -->
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="icon icon-2"
              >
                <path d="M12 5l0 14" />
                <path d="M5 12l14 0" />
              </svg>
              Create new report
            </a>
          </div>
        </div>
      </div>
    </div>
    <!-- END PAGE MODALS -->
    
    @include('partials.script')

    
  </body>
</html>
