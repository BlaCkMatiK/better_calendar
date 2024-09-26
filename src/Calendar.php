<?php
class Calendar
{
    public $active_year, $active_month, $active_day, $active_week, $format;
    public $events = [];
    public function __construct($date = null,  $week = null)
    {
        $this->active_year = $date != null ? date('Y', strtotime($date)) : date('Y');
        $this->active_month = $date != null ? date('m', strtotime($date)) : date('m');
        $this->active_week = $week != null ? $week : ($date != null ? date('W', strtotime($date)) : date('W'));
        $this->active_day = $date != null ? date('d', strtotime($date)) : date('d');
        $this->format = isset($_SESSION['view_method']) && $_SESSION['view_method'] === 'week' ? 'week' : 'month';
    }
    public function add_event($txt, $date, $days = 1, $color = '', $content = [])
    {
        $this->events[] = [$txt, $date, $days, $color, $content];
    }
    public function __toString()
    {
        $days = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        $html = '<div class="calendar">';
        $html .= '<div class="header">';
        $html .= '</div>';
        $html .= '<div class="days">';
        if ($this->format === 'week') {
            // Mode semaine : affichage des jours de lundi à vendredi
            $html .= $this->render_week_view($days);
        } else {
            // Mode mois : affichage du mois entier
            $html .= $this->render_month_view($days);
        }
        $html .= '</div>'; // fin des jours
        $html .= '</div>'; // fin du calendrier
        return $html;
    }
    private function render_week_view($days)
    {
        $html = '';
        // Affichage des noms des jours pour le mode semaine (lundi à vendredi)
        for ($i = 0; $i < 5; $i++) {
            $html .= '<div class="day_name">' . $days[$i] . '</div>';
        }

        // Calcul de la date du lundi de la semaine sélectionnée
        $start_of_week = new DateTime();
        $start_of_week->setISODate($this->active_year, $this->active_week); // ISODate permet de définir une date par numéro de semaine
        $start_of_week->modify('Monday this week'); // Assure que l'on commence bien au lundi

        // Affichage des 5 jours (lundi à vendredi)
        for ($i = 0; $i < 5; $i++) {
            $current_day = clone $start_of_week;
            $current_day->modify("+$i day");
            $html .= $this->render_day($current_day->format('d'));
        }

        return $html;
    }

    private function render_month_view($days)
    {
        $html = '';

        // Affichage des noms des jours pour le mode mois (lundi à dimanche)
        foreach ($days as $day) {
            $html .= '<div class="day_name">' . $day . '</div>';
        }

        // Nombre de jours dans le mois courant
        $num_days = date('t', strtotime($this->active_year . '-' . $this->active_month . '-01'));

        // Calcul du nombre de jours du mois précédent
        $num_days_last_month = date('t', strtotime('last month', strtotime($this->active_year . '-' . $this->active_month . '-01')));

        // Premier jour de la semaine pour le mois courant
        $first_day_of_week = date('N', strtotime($this->active_year . '-' . $this->active_month . '-01')) - 1;

        // Affichage des jours du mois précédent
        for ($i = $first_day_of_week; $i > 0; $i--) {
            $html .= '<div class="day_num ignore">' . ($num_days_last_month - $i + 1) . '</div>';
        }

        // Affichage des jours du mois courant
        for ($i = 1; $i <= $num_days; $i++) {
            $html .= $this->render_day($i);
        }

        // Affichage des jours du mois suivant pour compléter le calendrier
        for ($i = 1; $i <= (42 - $num_days - max($first_day_of_week, 0)); $i++) {
            $html .= '<div class="day_num ignore">' . $i . '</div>';
        }

        return $html;
    }

    private function render_day($day)
    {
        $date_str = $this->active_year . '-' . $this->active_month . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
        $selected = ($day == $this->active_day) ? ' selected' : '';
        $html = '<div class="day_num' . $selected . '">';
        $html .= '<span>' . $day . '</span>';

        // Affichage des événements associés à ce jour
        foreach ($this->events as $event) {
            for ($d = 0; $d <= ($event[2] - 1); $d++) {
                if (date('Y-m-d', strtotime($this->active_year . '-' . $this->active_month . '-' . $day . ' -' . $d . ' day')) == date('Y-m-d', strtotime($event[1]))) {
                    $html .= '<div class="event" style="background-color: ' . $event[3] . '">';
                    switch ($event[4][3]) {
                        case "Présentiel":
                            $html .= '<i class="fa-solid fa-school" title="Présentiel"></i>';
                            break;
                        case "E-Learning":
                            $html .= '<i class="fa-solid fa-globe" title="E-Learning"></i>';
                            break;
                        case "Distanciel":
                            $html .= '<i class="fa-solid fa-desktop" title="Distanciel"></i>';
                            break;
                        default:
                            $html .= '';
                    }

                    if ($this->format != 'week') {
                        $html .= $this->render_event_month($event);
                    } else {
                        $html .= $this->render_event_week($event);
                    }
                    $html .= '</div>';
                }
            }
        }
        $html .= '</div>';
        return $html;
    }

    private function render_event_month($event)
    {
        $html = '';
        $html .= '<div class="event_content">';
        $html .= '<span> Salle : ' . $event[4][0] . '</span>';
        $html .= '<span> Prof : ' . $event[4][1] . '</span>';
        $html .= '<span> Lieu : ' . $event[4][2] . '</span>';
        $html .= '<span> Type : ' . $event[4][3] . '</span>';
        $html .= '<span> Nom : ' . $event[0] . '</span>';
        $html .= '</div>';

        return $html;
    }

    private function render_event_week($event)
    {
        $html = '';
        $html .= '<div class="start">';
        $html .= '<span>' . date('H:i', strtotime($event[4][4])) . '</span>';
        $html .= '</div>';
        $html .= '<div class="title">';
        $html .= '<span>' . $event[0] . '</span>';
        $html .= '<br>';
        $html .= '<span class="prof">' . $event[4][1] . '</span>';
        $html .= '</div>';
        $html .= '<div class="event_content_week">';
        $html .= '</div>';
        $html .= '<div class="event_content_footer">';
        // $html .= '<span class="type">' . $event[4][3] . '</span>';
        $html .= '<span class="room">' . $event[4][0] . ' - ' . substr($event[4][2], 0, 1) . '</span>';
        $html .= '</div>';
        $html .= '<div class="end">';
        $html .= '<span>' . date('H:i', strtotime($event[4][5])) . '</span>';
        $html .= '<span>';

        // $html .= var_dump($event);
        switch ($event[4][6]) {
            case "3":
                $html .= '<i class="fa-solid fa-code" title="DEV"></i>';
                break;
            case "4":
                $html .= '<i class="fa-solid fa-network-wired" title="INFRA"></i>';
                break;
            default:
                $html .= '';
        }

        $html .= '</span>';
        $html .= '</div>';

        return $html;
    }
}
