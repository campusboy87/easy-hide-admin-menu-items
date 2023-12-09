# CB87 Hide Admin Menu Items

Плагин предоставляет удобный интерфейс для скрытия неиспользуемых пунктов меню сайдбара в админ-панели WordPress.

![Simple Hidden Menu - Animate Screenshort](https://raw.githubusercontent.com/campusboy87/simple-hidden-menu/assets/assets/shm-main.gif)

## Описание

Плагин **CB87 Hide Admin Menu Items** создан для обеспечения удобства использования административной панели WordPress, позволяя скрывать ненужные пункты меню. Это особенно полезно на сайтах с обширными административными панелями, где множество меню и настроек может затруднять навигацию и создавать путаницу 🎨

## Почему это удобно?

На некоторых сайтах в административной панели существует множество меню и различных настроек, которые могут быть редко используемыми или даже излишними. **CB87 Hide Admin Menu Items** позволяет администраторам сайта легко скрывать такие элементы, что повышает эффективность работы и улучшает общий опыт в управлении сайтом.

![Интерфейс плагина CB87 Hide Admin Menu Items](https://raw.githubusercontent.com/dan-zakirov/cb87-hami/dan_zakirov/assets/img/settings_submenu.png)

### Простота использования для обычного пользователя

В мире существует множество плагинов для скрытия меню в административной панели WordPress, но большинство из них требуют дополнительных настроек и дополнительного обучения для обычного пользователя. Наш плагин **CB87 Hide Admin Menu Items** отличается от аналогов своей простотой и интуитивной понятностью.

После активации плагина в админ-баре появляется удобный переключатель для включения или отключения скрытых меню. При наведении на этот переключатель вы моментально видите все ваши скрытые меню. Дополнительно, при наведении на любой пункт меню WordPress, появляется иконка, сигнализирующая о том, что это меню можно скрыть. Если вы выбрали конкретное меню для скрытия, оно легко перемещается в область всех скрытых меню.

В любой момент вы можете быстро отключить или скрыть меню, наглядно видя все скрытые элементы при наведении на переключатель. Таким образом, наш плагин предоставляет максимально простой и интуитивно понятный способ скрытия и управления меню, даже для пользователей без специальных навыков в области управления WordPress.

## Настройки

![Настойки плагина CB87 Hide Admin Menu Items](https://raw.githubusercontent.com/dan-zakirov/cb87-hami/dan_zakirov/assets/img/settings.png)

Плагин условно предоставляет два режима работы:

- **Глобальный режим:**
    - Позволяет скрывать меню глобально для всех пользователей в соответствии с общими настройками.

- **Индивидуальный режим для каждого пользователя:**
    - При активации этой опции, каждый пользователь может настроить видимость и скрытие меню согласно своим индивидуальным предпочтениям.

Таким образом, пользователи могут выбирать между глобальным режимом и режимом индивидуальных настроек в зависимости от своих потребностей. Создание страницы настроек предоставляет удобный способ управления этими параметрами.

Страница настроек плагина доступна в разделе "Настройки" административной панели WordPress по пути "Настройки > Настройки меню".

### [ ] Настройки для каждого пользователя

При установке флажка каждый пользователь может сохранять свои настройки индивидуально. Это предоставляет возможность настроить видимость пунктов меню для каждого пользователя в соответствии с их предпочтениями.

Примечание: Флажок, о котором идет речь, позволяет активировать или деактивировать функционал настройки для каждого пользователя. При включенной опции сохранения, настройки хранятся в `user_meta`, а при отключенной - в `get_option`.

### [ ] Скрытие дочерних меню

Активируйте эту опцию, чтобы легко управлять видимостью дочерних меню в вашей административной панели WordPress. Когда эта опция включена, вы сможете скрывать дочерние меню, что особенно полезно, когда ваша административная панель насыщена множеством плагинов, и некоторые пункты меню могут вызывать дискомфорт или отвлекать вас. Пользуйтесь WordPress более эффективно, выбирая, какие дочерние меню отображать, и создавая более чистый и удобный интерфейс для вашей работы.

## Инструкции по установке

Чтобы установить плагин CB87 Hide Admin Menu Items с GitHub, следуйте простым шагам:

1. **Скачайте ZIP-архив:**
    - Перейдите на [страницу репозитория](https://github.com/campusboy87/cb87-hami).
    - Нажмите на кнопку "Code" и выберите "Download ZIP".

2. **Переместите плагин в WordPress:**
    - Перейдите в административную панель WordPress.
    - В разделе "Плагины" выберите "Добавить новый".
    - Нажмите кнопку "Загрузить плагин" и выберите ранее скачанный ZIP-файл.
    - Установите и активируйте плагин.

3. **Настройка плагина:**
    - После активации, плагин сразу готов к работе, однако вы можете более точно настроить плагин. Перейти в раздел "Настройки" административной панели и выбрать "Настройки меню".
    - Настройте параметры в соответствии с вашими предпочтениями, выбрав глобальный режим или индивидуальный режим для каждого пользователя.

Теперь плагин готов к использованию, и вы можете наслаждаться улучшенной управляемостью меню в вашей административной панели WordPress!

## Changelog

**1.3:**
- Добавлена новая опция которая позволет скрывать дочернеие меню

**1.2:**
- Добавлена поддержка WordPress 6.4.
- Добавлена страница настроек.

**1.1:**
- Исправлены мелкие ошибки, связанные с видимостью пунктов меню.
- Улучшена производительность для крупных установок WordPress.