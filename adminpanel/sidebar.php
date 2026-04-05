<head>
  <link rel="icon" href="favicon.ico">
  <link href="style.css" rel="stylesheet">
</head>
<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
  class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 duration-300 ease-linear dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0"
  @click.outside="sidebarToggle = false">
  <!-- SIDEBAR HEADER -->
  <div :class="sidebarToggle ? 'justify-center' : 'justify-between'"
    class="sidebar-header flex items-center gap-2 pb-7 pt-8">
    <a href="index.php">
      <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
        <img class="dark:hidden w-24 h-8 object-contain" src="src/images/logo/logo2.png" alt="Logo" />
        <img class="hidden dark:block w-24 h-8 object-contain" src="src/images/logo/logo2.png" alt="Logo" />
      </span>

      <img class="logo-icon w-10 h-4 object-contain" :class="sidebarToggle ? 'lg:block' : 'hidden'"
        src="src/images/logo/logo2.png" alt="Logo" />
    </a>
  </div>
  <!-- SIDEBAR HEADER -->

  <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
    <!-- Sidebar Menu -->
    <nav x-data="{
        selected: '', // HAPUS $persist DI SINI
        currentPage: '',
        hoveredItem: '',
        
        init() {
          // Deteksi halaman saat ini dari URL
          this.detectCurrentPage();
          // Set selected berdasarkan halaman aktif
          this.setSelectedFromPage();
        },
        
        detectCurrentPage() {
          const path = window.location.pathname;
          if (path.includes('index.php')) this.currentPage = 'dashboard';
          else if (path.includes('dataadmin.php')) this.currentPage = 'dataadmin';
          else if (path.includes('dataperusahaan.php')) this.currentPage = 'dataperusahaan';
          else if (path.includes('datapelamar.php')) this.currentPage = 'datapelamar';
          else if (path.includes('datalowongan.php')) this.currentPage = 'datalowongan';
          else if (path.includes('datalamaran.php')) this.currentPage = 'datalamaran';
          else if (path.includes('datakategori.php')) this.currentPage = 'datakategori';
          else if (path.includes('datajenis.php')) this.currentPage = 'datajenis';
          else if (path.includes('datalokasi.php')) this.currentPage = 'datalokasi';
          else if (path.includes('form-elements.php')) this.currentPage = 'formElements';
          else if (path.includes('calendar.php')) this.currentPage = 'calendar';
          else this.currentPage = '';
        },
        
        setSelectedFromPage() {
          // Reset selected terlebih dahulu
          this.selected = '';
          
          // Jika halaman termasuk dalam Data Master
          if (['datakategori', 'datajenis', 'datalokasi', 'dataperusahaan', 'datapelamar'].includes(this.currentPage)) {
            this.selected = 'data-master';
          }
          // Jika halaman termasuk dalam Rekrutmen
          else if (['datalowongan', 'datalamaran'].includes(this.currentPage)) {
            this.selected = 'rekrutmen';
          }
          // Jika halaman termasuk dalam Manajemen Pengguna
          else if (['dataadmin'].includes(this.currentPage)) {
            this.selected = 'manajemen-pengguna';
          }
        },
        
        // Helper functions untuk kondisi
        isManajemenPenggunaPage() {
          return ['dataadmin'].includes(this.currentPage);
        },
        
        isRekrutmenPage() {
          return ['datalowongan', 'datalamaran'].includes(this.currentPage);
        },
        
        isDataMasterPage() {
          return ['datakategori', 'datajenis', 'datalokasi', 'dataperusahaan', 'datapelamar'].includes(this.currentPage);
        },
        
        isActiveSubmenu(submenu) {
          return this.currentPage === submenu;
        },
        
        // Fungsi untuk hover effect
        setHovered(item) {
          this.hoveredItem = item;
        },
        
        clearHovered() {
          this.hoveredItem = '';
        },
        
        isHovered(item) {
          return this.hoveredItem === item;
        },
        
        // Fungsi untuk toggle dropdown dengan ID unik
        toggleDropdown(dropdownId) {
          if (this.selected === dropdownId) {
            this.selected = '';
          } else {
            this.selected = dropdownId;
          }
        },
        
        isDropdownOpen(dropdownId) {
          return this.selected === dropdownId;
        }
      }">
      <!-- Menu Group -->
      <div>
        <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
            MENU
          </span>

          <svg :class="sidebarToggle ? 'lg:block hidden' : 'hidden'" class="menu-group-icon mx-auto fill-current"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
              fill="" />
          </svg>
        </h3>

        <ul class="mb-6 flex flex-col gap-4">
          <!-- Menu Item Dashboard (TANPA DROPDOWN) -->
          <li>
            <a href="index.php" class="menu-item group"
              :class="currentPage === 'dashboard' ? 'menu-item-active' : 'menu-item-inactive'"
              @mouseenter="setHovered('dashboard')" @mouseleave="clearHovered()">
              <svg :class="currentPage === 'dashboard' ? 'menu-item-icon-active' : 'menu-item-icon-inactive'" width="24"
                height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M5.5 3.25C4.25736 3.25 3.25 4.25736 3.25 5.5V8.99998C3.25 10.2426 4.25736 11.25 5.5 11.25H9C10.2426 11.25 11.25 10.2426 11.25 8.99998V5.5C11.25 4.25736 10.2426 3.25 9 3.25H5.5ZM4.75 5.5C4.75 5.08579 5.08579 4.75 5.5 4.75H9C9.41421 4.75 9.75 5.08579 9.75 5.5V8.99998C9.75 9.41419 9.41421 9.74998 9 9.74998H5.5C5.08579 9.74998 4.75 9.41419 4.75 8.99998V5.5ZM5.5 12.75C4.25736 12.75 3.25 13.7574 3.25 15V18.5C3.25 19.7426 4.25736 20.75 5.5 20.75H9C10.2426 20.75 11.25 19.7427 11.25 18.5V15C11.25 13.7574 10.2426 12.75 9 12.75H5.5ZM4.75 15C4.75 14.5858 5.08579 14.25 5.5 14.25H9C9.41421 14.25 9.75 14.5858 9.75 15V18.5C9.75 18.9142 9.41421 19.25 9 19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5V15ZM12.75 5.5C12.75 4.25736 13.7574 3.25 15 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V8.99998C20.75 10.2426 19.7426 11.25 18.5 11.25H15C13.7574 11.25 12.75 10.2426 12.75 8.99998V5.5ZM15 4.75C14.5858 4.75 14.25 5.08579 14.25 5.5V8.99998C14.25 9.41419 14.5858 9.74998 15 9.74998H18.5C18.9142 9.74998 19.25 9.41419 19.25 8.99998V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H15ZM15 12.75C13.7574 12.75 12.75 13.7574 12.75 15V18.5C12.75 19.7426 13.7574 20.75 15 20.75H18.5C19.7426 20.75 20.75 19.7427 20.75 18.5V15C20.75 13.7574 19.7426 12.75 18.5 12.75H15ZM14.25 15C14.25 14.5858 14.5858 14.25 15 14.25H18.5C18.9142 14.25 19.25 14.5858 19.25 15V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15C14.5858 19.25 14.25 18.9142 14.25 18.5V15Z"
                  fill="" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                Dashboard
              </span>
            </a>
          </li>
          <!-- Menu Item Dashboard -->
          <!-- Menu Item Data Master -->
          <li>
            <a href="#" @click.prevent="toggleDropdown('data-master')" class="menu-item group"
              :class="(isDropdownOpen('data-master') || isDataMasterPage()) ? 'menu-item-active' : 'menu-item-inactive'"
              @mouseenter="setHovered('data-master')" @mouseleave="clearHovered()">
              <svg
                :class="(isDropdownOpen('data-master') || isDataMasterPage() || isHovered('data-master')) ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M3.25 5.5C3.25 4.25736 4.25736 3.25 5.5 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V18.5C20.75 19.7426 19.7426 20.75 18.5 20.75H5.5C4.25736 20.75 3.25 19.7426 3.25 18.5V5.5ZM5.5 4.75C5.08579 4.75 4.75 5.08579 4.75 5.5V8.58325L19.25 8.58325V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H5.5ZM19.25 10.0833H15.416V13.9165H19.25V10.0833ZM13.916 10.0833L10.083 10.0833V13.9165L13.916 13.9165V10.0833ZM8.58301 10.0833H4.75V13.9165H8.58301V10.0833ZM4.75 18.5V15.4165H8.58301V19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5ZM10.083 19.25V15.4165L13.916 15.4165V19.25H10.083ZM15.416 19.25V15.4165H19.25V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15.416Z"
                  fill="" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                Data Master
              </span>

              <svg class="menu-item-arrow absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current"
                :class="[(isDropdownOpen('data-master') || isDataMasterPage() || isHovered('data-master')) ? 'rotate-180' : '', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>

            <!-- Dropdown Menu Start -->
            <div class="translate transform overflow-hidden"
              :class="(isDropdownOpen('data-master') || isDataMasterPage()) ? 'block' :'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="menu-dropdown mt-2 flex flex-col gap-1 pl-9">

                <!-- Data Pelamar -->
                <li>
                  <a href="datapelamar.php" class="menu-dropdown-item group"
                    :class="isActiveSubmenu('datapelamar') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                    @mouseenter="setHovered('datapelamar')" @mouseleave="clearHovered()">
                    Data Pelamar
                  </a>
                </li>
                <!-- Data Perusahaan -->
                <li>
                  <a href="dataperusahaan.php" class="menu-dropdown-item group"
                    :class="isActiveSubmenu('dataperusahaan') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                    @mouseenter="setHovered('dataperusahaan')" @mouseleave="clearHovered()">
                    Data Perusahaan
                  </a>
                </li>
                <!-- Data Kategori -->
                <li>
                  <a href="datakategori.php" class="menu-dropdown-item group"
                    :class="isActiveSubmenu('datakategori') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                    @mouseenter="setHovered('datakategori')" @mouseleave="clearHovered()">
                    Data Kategori
                  </a>
                </li>

                <!-- Data Jenis -->
                <li>
                  <a href="datajenis.php" class="menu-dropdown-item group"
                    :class="isActiveSubmenu('datajenis') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                    @mouseenter="setHovered('datajenis')" @mouseleave="clearHovered()">
                    Data Jenis
                  </a>
                </li>

                <!-- Data Lokasi -->
                <li>
                  <a href="datalokasi.php" class="menu-dropdown-item group"
                    :class="isActiveSubmenu('datalokasi') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                    @mouseenter="setHovered('datalokasi')" @mouseleave="clearHovered()">
                    Data Lokasi
                  </a>
                </li>
              </ul>
            </div>
            <!-- Dropdown Menu End -->
          </li>
          <!-- Menu Item Data Master -->


          <!-- Menu Item Rekrutmen -->
          <li>
            <a href="#" @click.prevent="toggleDropdown('rekrutmen')" class="menu-item group"
              :class="(isDropdownOpen('rekrutmen') || isRekrutmenPage()) ? 'menu-item-active' : 'menu-item-inactive'"
              @mouseenter="setHovered('rekrutmen')" @mouseleave="clearHovered()">
              <svg
                :class="(isDropdownOpen('rekrutmen') || isRekrutmenPage() || isHovered('rekrutmen')) ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M3.25 5.5C3.25 4.25736 4.25736 3.25 5.5 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V18.5C20.75 19.7426 19.7426 20.75 18.5 20.75H5.5C4.25736 20.75 3.25 19.7426 3.25 18.5V5.5ZM5.5 4.75C5.08579 4.75 4.75 5.08579 4.75 5.5V8.58325L19.25 8.58325V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H5.5ZM19.25 10.0833H15.416V13.9165H19.25V10.0833ZM13.916 10.0833L10.083 10.0833V13.9165L13.916 13.9165V10.0833ZM8.58301 10.0833H4.75V13.9165H8.58301V10.0833ZM4.75 18.5V15.4165H8.58301V19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5ZM10.083 19.25V15.4165L13.916 15.4165V19.25H10.083ZM15.416 19.25V15.4165H19.25V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15.416Z"
                  fill="" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                Rekrutmen
              </span>

              <svg class="menu-item-arrow absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current"
                :class="[(isDropdownOpen('rekrutmen') || isRekrutmenPage() || isHovered('rekrutmen')) ? 'rotate-180' : '', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>

            <!-- Dropdown Menu Start -->
            <div class="translate transform overflow-hidden"
              :class="(isDropdownOpen('rekrutmen') || isRekrutmenPage()) ? 'block' :'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="menu-dropdown mt-2 flex flex-col gap-1 pl-9">
                <!-- Data Lowongan -->
                <li>
                  <a href="datalowongan.php" class="menu-dropdown-item group"
                    :class="isActiveSubmenu('datalowongan') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                    @mouseenter="setHovered('datalowongan')" @mouseleave="clearHovered()">
                    Data Lowongan
                  </a>
                </li>

                <!-- Data Lamaran -->
                <li>
                  <a href="datalamaran.php" class="menu-dropdown-item group"
                    :class="isActiveSubmenu('datalamaran') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                    @mouseenter="setHovered('datalamaran')" @mouseleave="clearHovered()">
                    Data Lamaran
                  </a>
                </li>
              </ul>
            </div>
            <!-- Dropdown Menu End -->
          </li>
          <!-- Menu Item Rekrutmen -->
          <!-- Menu Item Manajemen Pengguna -->
          <li>
            <a href="#" @click.prevent="toggleDropdown('manajemen-pengguna')" class="menu-item group"
              :class="(isDropdownOpen('manajemen-pengguna') || isManajemenPenggunaPage()) ? 'menu-item-active' : 'menu-item-inactive'"
              @mouseenter="setHovered('manajemen-pengguna')" @mouseleave="clearHovered()">
              <svg
                :class="(isDropdownOpen('manajemen-pengguna') || isManajemenPenggunaPage() || isHovered('manajemen-pengguna')) ? 'menu-item-icon-active' : 'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M3.25 5.5C3.25 4.25736 4.25736 3.25 5.5 3.25H18.5C19.7426 3.25 20.75 4.25736 20.75 5.5V18.5C20.75 19.7426 19.7426 20.75 18.5 20.75H5.5C4.25736 20.75 3.25 19.7426 3.25 18.5V5.5ZM5.5 4.75C5.08579 4.75 4.75 5.08579 4.75 5.5V8.58325L19.25 8.58325V5.5C19.25 5.08579 18.9142 4.75 18.5 4.75H5.5ZM19.25 10.0833H15.416V13.9165H19.25V10.0833ZM13.916 10.0833L10.083 10.0833V13.9165L13.916 13.9165V10.0833ZM8.58301 10.0833H4.75V13.9165H8.58301V10.0833ZM4.75 18.5V15.4165H8.58301V19.25H5.5C5.08579 19.25 4.75 18.9142 4.75 18.5ZM10.083 19.25V15.4165L13.916 15.4165V19.25H10.083ZM15.416 19.25V15.4165H19.25V18.5C19.25 18.9142 18.9142 19.25 18.5 19.25H15.416Z"
                  fill="" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                Manajemen Pengguna
              </span>

              <svg class="menu-item-arrow absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current"
                :class="[(isDropdownOpen('manajemen-pengguna') || isManajemenPenggunaPage() || isHovered('manajemen-pengguna')) ? 'rotate-180' : '', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>

            <!-- Dropdown Menu Start -->
            <div class="translate transform overflow-hidden"
              :class="(isDropdownOpen('manajemen-pengguna') || isManajemenPenggunaPage()) ? 'block' :'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="menu-dropdown mt-2 flex flex-col gap-1 pl-9">

                <!-- Data Admin -->
                <li>
                  <a href="dataadmin.php" class="menu-dropdown-item group"
                    :class="isActiveSubmenu('dataadmin') ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                    @mouseenter="setHovered('dataadmin')" @mouseleave="clearHovered()">
                    Data Admin
                  </a>
                </li>


              </ul>
            </div>
            <!-- Dropdown Menu End -->
          </li>
          <!-- Menu Item Manajemen Pengguna -->


        </ul>
      </div>
    </nav>
    <!-- Sidebar Menu -->
  </div>
</aside>
<script defer src="bundle.js"></script>