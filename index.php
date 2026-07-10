<?php
declare(strict_types=1);
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <!-- Mobile-first viewport for Android phones -->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover" />
  <title>Badminton Tournament Manager</title>
  <style>
    :root {
      --primary: #0f766e;
      --primary-strong: #115e59;
      --primary-soft: #ccfbf1;
      --bg: #f4f7f6;
      --surface: #ffffff;
      --surface-alt: #f8fafc;
      --border: #d7e1df;
      --border-strong: #afbfbb;
      --text: #15201d;
      --muted: #64736f;
      --accent: #16a34a;
      --danger: #dc2626;
      --danger-soft: #fee2e2;
      --shadow-soft: 0 12px 30px rgba(15, 23, 42, 0.08);
      --focus-ring: 0 0 0 3px rgba(20, 184, 166, 0.18);
    }

    * {
      box-sizing: border-box;
    }

    html {
      width: 100%;
      min-height: 100%;
      overflow-x: hidden;
    }

    body {
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      margin: 0;
      padding: 16px 12px 28px;
      min-height: 100vh;
      width: 100%;
      max-width: 100vw;
      overflow-x: hidden;
      background: var(--bg);
      color: var(--text);
      display: flex;
      flex-direction: column;
      align-items: stretch;
      -webkit-tap-highlight-color: transparent;
    }

    .skipLink {
      position: fixed;
      left: 12px;
      top: 12px;
      z-index: 200;
      transform: translateY(-140%);
      padding: 10px 12px;
      border-radius: 8px;
      background: var(--primary-strong);
      color: #ffffff;
      font-weight: 750;
      text-decoration: none;
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.18);
      transition: transform 0.15s ease;
    }

    .skipLink:focus {
      transform: translateY(0);
      outline: none;
      box-shadow: var(--focus-ring), 0 10px 24px rgba(15, 23, 42, 0.18);
    }

    .srOnly {
      position: absolute !important;
      width: 1px !important;
      height: 1px !important;
      padding: 0 !important;
      margin: -1px !important;
      overflow: hidden !important;
      clip: rect(0, 0, 0, 0) !important;
      white-space: nowrap !important;
      border: 0 !important;
    }

    h1 {
      width: min(100%, 1040px);
      margin: 0 auto 4px;
      padding: 0 2px;
      color: var(--text);
      font-size: clamp(23px, 4.5vw, 32px);
      font-weight: 760;
      line-height: 1.15;
      letter-spacing: 0;
    }

    h3 {
      color: var(--text);
      font-size: 20px;
      line-height: 1.2;
      letter-spacing: 0;
    }

    h4 {
      color: var(--text);
      font-size: 15px;
      line-height: 1.3;
      letter-spacing: 0;
    }

    .subTitle {
      width: min(100%, 1040px);
      margin: 0 auto 14px;
      padding: 0 2px;
      color: var(--muted);
      font-size: 13px;
      line-height: 1.45;
    }

    .section {
      background: var(--surface);
      padding: clamp(14px, 2.6vw, 20px);
      margin: 0 auto 14px;
      border-radius: 8px;
      box-shadow: var(--shadow-soft);
      width: min(100%, 1040px);
      max-width: 100%;
      border: 1px solid var(--border);
      position: relative;
      overflow: hidden;
    }

    .section::before {
      display: none;
    }

    .section > * {
      position: relative;
      z-index: 1;
    }

    .featureView.hidden {
      display: none !important;
    }

    .featureView:not(.hidden) {
      display: block;
    }

    .workspaceHeader {
      display: flex;
      align-items: center;
      gap: 14px;
      margin: -2px 0 18px;
      padding: 16px;
      border: 1px solid #bde8e1;
      border-radius: 12px;
      background: linear-gradient(135deg, #f0fdfa, #f8fafc 72%);
      overflow: hidden;
    }

    .workspaceHeaderIcon {
      flex: 0 0 50px;
      width: 50px;
      height: 50px;
      display: grid;
      place-items: center;
      border-radius: 12px;
      color: #ffffff;
      background: linear-gradient(135deg, var(--primary), var(--primary-strong));
      box-shadow: 0 8px 18px rgba(15, 118, 110, 0.2);
      font-size: 25px;
      line-height: 1;
    }

    .workspaceHeader h3 {
      margin: 0 0 4px;
      font-size: clamp(19px, 3vw, 24px);
    }

    .workspaceHeader .hint {
      margin: 0;
    }

    .workspacePanel,
    .dataSurface {
      border: 1px solid var(--border);
      border-radius: 10px;
      background: var(--surface-alt);
    }

    .workspacePanel {
      padding: 14px;
      margin-bottom: 12px;
    }

    .dataSurface {
      min-height: 54px;
      padding: 10px;
      margin-top: 12px;
    }

    .workspaceActionBar {
      margin-top: 10px;
      padding-top: 10px;
      border-top: 1px solid var(--border);
    }

    .tournamentSetupGrid {
      display: flex;
      flex-direction: column;
      gap: 12px;
      max-width: 780px;
    }

    .tournamentField {
      display: grid;
      grid-template-columns: minmax(170px, 220px) minmax(0, 1fr);
      gap: 8px 16px;
      align-items: center;
      min-width: 0;
      width: 100%;
    }

    .tournamentField label {
      margin: 0;
      color: #334155;
      font-size: 12px;
      font-weight: 750;
      letter-spacing: 0;
      line-height: 1.25;
      text-transform: none;
    }

    .tournamentField input,
    .tournamentField select {
      width: 100%;
      max-width: 460px;
      min-height: 42px;
      margin-bottom: 0;
      background: #ffffff;
    }

    .tournamentField .hint {
      grid-column: 2;
      margin: -2px 0 0;
    }

    .tournamentField-wide {
      grid-column: auto;
    }

    .tournamentField-compact {
      max-width: none;
    }

    .tournamentField-compact input[type=number] {
      max-width: 126px;
    }

    .tournamentField-nested,
    .tournamentNestedFields {
      margin-left: 24px;
      padding-left: 18px;
      border-left: 3px solid var(--border);
    }

    .tournamentNestedFields,
    .tournamentTeamFields {
      display: flex;
      flex-direction: column;
      gap: 12px;
      width: 100%;
    }

    .tournamentNestedFields .tournamentField {
      margin-left: 0;
      padding-left: 0;
      border-left: 0;
    }

    .tournamentTeamFields:focus {
      outline: none;
    }

    .tournamentTeamsPreview {
      margin-top: 14px;
      background: #ffffff;
    }

    #sessionBar {
      padding: 0;
      margin-bottom: 10px;
      border: 0;
      background: transparent;
      box-shadow: none;
      overflow: visible;
    }

    .accountBar {
      display: flex;
      justify-content: flex-end;
      width: 100%;
    }

    .accountPill {
      display: inline-flex;
      align-items: center;
      justify-content: flex-end;
      gap: 10px;
      min-width: 0;
      padding: 7px 8px 7px 12px;
      border: 1px solid var(--border);
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.9);
      box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
    }

    .accountLabel {
      color: var(--muted);
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      white-space: nowrap;
    }

    .accountName {
      max-width: 240px;
      color: var(--text);
      font-size: 13px;
      font-weight: 800;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    #sessionLogoutBtn {
      min-height: 32px;
      padding: 7px 11px;
      border-radius: 999px;
      font-size: 12px;
    }

    .tournamentFlow {
      display: grid;
      gap: 14px;
    }

    .tournamentChoiceGrid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    .tournamentChoiceCard {
      min-height: 168px;
      padding: 18px;
      align-items: flex-start;
      justify-content: space-between;
      flex-direction: column;
      gap: 14px;
      text-align: left;
      color: var(--text);
      background: #ffffff;
      border: 1px solid var(--border);
      box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
    }

    .tournamentChoiceCard:hover:not(:disabled) {
      border-color: #5eead4;
      background: #f8fffd;
    }

    .tournamentChoiceIcon {
      position: relative;
      width: 54px;
      height: 54px;
      border-radius: 14px;
      background: linear-gradient(135deg, #ccfbf1, #ffffff);
      border: 1px solid #99f6e4;
      box-shadow: inset 0 0 0 6px rgba(20, 184, 166, 0.08);
    }

    .tournamentChoiceIcon.createIcon::before,
    .tournamentChoiceIcon.createIcon::after {
      content: '';
      position: absolute;
      left: 50%;
      top: 50%;
      width: 24px;
      height: 4px;
      border-radius: 999px;
      background: var(--primary-strong);
      transform: translate(-50%, -50%);
    }

    .tournamentChoiceIcon.createIcon::after {
      width: 4px;
      height: 24px;
    }

    .tournamentChoiceIcon.loadIcon::before {
      content: '';
      position: absolute;
      left: 11px;
      right: 11px;
      bottom: 13px;
      height: 24px;
      border-radius: 5px;
      background: var(--primary-strong);
      box-shadow: 0 -8px 0 -3px #14b8a6;
    }

    .tournamentChoiceIcon.loadIcon::after {
      content: '';
      position: absolute;
      left: 15px;
      top: 14px;
      width: 17px;
      height: 9px;
      border-radius: 5px 5px 0 0;
      background: #14b8a6;
    }

    .tournamentChoiceTitle {
      display: block;
      font-size: 18px;
      font-weight: 850;
      line-height: 1.2;
    }

    .tournamentChoiceText {
      display: block;
      margin-top: 5px;
      color: var(--muted);
      font-size: 13px;
      line-height: 1.45;
    }

    .tournamentFlowPanel {
      padding: 16px;
      border: 1px solid var(--border);
      border-radius: 10px;
      background: var(--surface-alt);
    }

    .tournamentFlowHeader {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 12px;
    }

    .tournamentFlowHeader h4 {
      margin: 0 0 4px;
      font-size: 16px;
    }

    .tournamentBackBtn {
      flex: 0 0 auto;
      min-height: 34px;
      padding: 7px 10px;
    }

    #createTournamentPanel,
    #loadTournamentPanel {
      align-self: stretch;
      padding: 14px;
      border: 1px solid var(--border);
      border-radius: 10px;
      background: var(--surface-alt);
    }

    #tournamentSetupPanel {
      margin-top: 14px;
      padding: 14px;
      border: 1px solid #99f6e4;
      border-radius: 10px;
      background: #f0fdfa;
    }

    #viewShuttles .shuttleSummary .statCard:first-child {
      border-color: #5eead4;
      background: #f0fdfa;
    }

    #viewShuttles .shuttleSummary .statCard:first-child .statValue {
      color: var(--primary-strong);
    }

    .featureView table tbody tr:nth-child(even) td {
      background: #fbfdfd;
    }

    .featureView table tbody tr:hover td {
      background: #f0fdfa;
    }

    #finalLeaderboardOutput tbody tr:nth-child(1) td {
      background: #fffbeb;
      font-weight: 750;
    }

    #finalLeaderboardOutput tbody tr:nth-child(2) td {
      background: #f8fafc;
    }

    #finalLeaderboardOutput tbody tr:nth-child(3) td {
      background: #fff7ed;
    }

    #finalLeaderboardOutput tbody tr:nth-child(-n+3) td:first-child {
      font-size: 18px;
    }

    #viewLeaderboard .workspaceHeader .hint {
      display: none;
    }

    #leaderboardPeriodControls {
      margin-top: 12px;
    }

    #pointsTableOutput tbody tr:first-child td {
      background: #ecfdf5;
      color: #14532d;
      font-weight: 750;
    }

    #playersList .listItem {
      border-radius: 7px;
      transition: background 0.15s ease, transform 0.15s ease;
    }

    #playersList .listItem:hover {
      background: #ffffff;
      transform: translateY(-1px);
    }

    .playersManager {
      display: grid;
      grid-template-columns: minmax(260px, 0.95fr) minmax(320px, 1.15fr);
      gap: 14px;
      align-items: start;
    }

    .playersControlPanel {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .playersControlPanel .workspacePanel {
      margin-bottom: 0;
    }

    .playersRosterPanel {
      min-width: 0;
    }

    .playersRosterHeader {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 12px;
    }

    .playersRosterHeader h4 {
      margin: 0;
    }

    .playersHeaderActions {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .playersRosterGrid {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .playerAddRow {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 44px;
      gap: 8px;
      align-items: end;
    }

    .playerAddRow input {
      margin-bottom: 0;
    }

    .bulkPlayersPanel {
      margin-top: 12px;
      padding-top: 12px;
      border-top: 1px solid var(--border);
    }

    .playerCard {
      display: grid;
      grid-template-columns: 54px minmax(0, 1fr) auto;
      gap: 12px;
      align-items: center;
      padding: 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      background: #ffffff;
      box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
    }

    .playerAvatar,
    .awardPlayerPhoto {
      display: grid;
      place-items: center;
      overflow: hidden;
      border-radius: 999px;
      background: linear-gradient(135deg, #ccfbf1, #f8fafc);
      color: var(--primary-strong);
      font-weight: 900;
      text-transform: uppercase;
    }

    .playerAvatar {
      width: 54px;
      height: 54px;
      border: 2px solid #ffffff;
      box-shadow: 0 0 0 1px var(--border);
      font-size: 18px;
    }

    .playerAvatar img,
    .awardPlayerPhoto img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .playerCardBody {
      min-width: 0;
    }

    .playerDisplayName {
      color: var(--text);
      font-size: 15px;
      font-weight: 800;
      line-height: 1.25;
      overflow-wrap: anywhere;
    }

    .playerNameInput {
      display: none;
      margin-bottom: 0 !important;
      font-weight: 750;
    }

    .playerCard.isEditing .playerDisplayName {
      display: none;
    }

    .playerCard.isEditing .playerNameInput {
      display: block;
    }

    .playerCardActions {
      display: flex;
      flex-wrap: nowrap;
      gap: 8px;
      align-items: center;
      justify-content: flex-end;
    }

    .playerIconBtn,
    .photoUploadBtn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      min-width: 36px;
      height: 36px;
      min-height: 36px;
      padding: 0;
      border: 1px solid var(--border-strong);
      border-radius: 8px;
      background: var(--surface-alt);
      color: var(--primary-strong);
      cursor: pointer;
      font-size: 17px;
      font-weight: 800;
      letter-spacing: 0;
      box-shadow: none;
      line-height: 1;
    }

    .playerIconBtn:hover:not(:disabled),
    .photoUploadBtn:hover {
      background: #ecfeff;
      border-color: #5eead4;
    }

    .playerIconBtn:disabled {
      cursor: not-allowed;
      opacity: 0.45;
    }

    .addPlayerIconBtn {
      width: 44px;
      min-width: 44px;
      height: 42px;
      min-height: 42px;
      background: var(--primary);
      border-color: var(--primary);
      color: #ffffff;
      font-size: 24px;
    }

    .toggleBulkPlayersBtn[aria-expanded="true"] {
      background: #ecfeff;
      border-color: #5eead4;
      color: var(--primary-strong);
    }

    .playerRemoveBtn {
      background: var(--danger);
      border-color: var(--danger);
      color: #ffffff;
    }

    .photoUploadInput {
      position: absolute;
      width: 1px;
      height: 1px;
      opacity: 0;
      pointer-events: none;
    }

    .historyCalendar {
      padding: 14px;
      border: 1px solid var(--border);
      border-radius: 12px;
      background: var(--surface-alt);
    }

    .historyCalendarHeader {
      display: grid;
      grid-template-columns: 38px minmax(0, 1fr) 38px;
      gap: 8px;
      align-items: center;
      margin-bottom: 10px;
    }

    .historyCalendarHeader button {
      width: 38px;
      min-width: 38px;
      height: 36px;
      min-height: 36px;
      padding: 0;
      background: #ffffff;
      color: var(--primary-strong);
      border-color: var(--border);
      font-size: 18px;
    }

    .historyCalendarMonth {
      text-align: center;
      font-size: 16px;
      font-weight: 800;
    }

    .historyCalendarWeekdays,
    .historyCalendarGrid {
      display: grid;
      grid-template-columns: repeat(7, minmax(0, 1fr));
      gap: 5px;
    }

    .historyCalendarWeekdays span {
      padding: 3px 0;
      color: var(--muted);
      text-align: center;
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .historyCalendarGrid {
      margin-top: 4px;
    }

    .historyCalendarDay,
    .historyCalendarBlank {
      min-width: 0;
      aspect-ratio: 1;
    }

    .historyCalendarDay {
      position: relative;
      width: 100%;
      min-height: 34px;
      padding: 2px;
      color: var(--text);
      background: #ffffff;
      border-color: var(--border);
      border-radius: 8px;
      box-shadow: none;
      font-size: 12px;
    }

    .historyCalendarDay.hasTournaments {
      color: var(--primary-strong);
      border-color: #5eead4;
      background: #f0fdfa;
      font-weight: 800;
    }

    .historyCalendarDay.hasTournaments::after {
      content: '';
      position: absolute;
      left: 50%;
      bottom: 4px;
      width: 4px;
      height: 4px;
      border-radius: 50%;
      background: var(--primary);
      transform: translateX(-50%);
    }

    .historyCalendarDay.selected {
      color: #ffffff;
      border-color: var(--primary);
      background: var(--primary);
      box-shadow: var(--focus-ring);
    }

    .historyCalendarDay.selected::after {
      background: #ffffff;
    }

    .historyCalendarStatus {
      margin: 10px 0 0;
      color: var(--muted);
      font-size: 12px;
      text-align: center;
    }

    @media (max-width: 600px) {
      .workspaceHeader {
        align-items: flex-start;
        padding: 13px;
      }

      .workspaceHeaderIcon {
        flex-basis: 42px;
        width: 42px;
        height: 42px;
        font-size: 21px;
      }

      .workspacePanel,
      .dataSurface,
      #createTournamentPanel,
      #loadTournamentPanel,
      #tournamentSetupPanel {
        padding: 11px;
      }

      .accountBar {
        justify-content: stretch;
      }

      .accountPill {
        width: 100%;
        border-radius: 10px;
      }

      .accountName {
        max-width: none;
        flex: 1;
      }

      .tournamentChoiceGrid {
        grid-template-columns: 1fr;
      }

      .tournamentSetupGrid,
      .tournamentTeamFields {
        grid-template-columns: 1fr;
      }

      .tournamentField {
        grid-template-columns: 1fr;
        gap: 6px;
      }

      .tournamentField .hint {
        grid-column: 1;
      }

      .tournamentField-wide,
      .tournamentTeamFields {
        grid-column: 1;
      }

      .tournamentField input,
      .tournamentField select {
        max-width: none;
      }

      .tournamentField-nested,
      .tournamentNestedFields {
        margin-left: 0;
        padding-left: 12px;
      }

      .tournamentField-compact {
        max-width: none;
      }

      .tournamentChoiceCard {
        min-height: 132px;
      }

      .tournamentFlowHeader {
        align-items: stretch;
        flex-direction: column;
      }

      .tournamentBackBtn {
        width: 100%;
      }
    }

    .row {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      align-items: center;
      width: 100%;
    }

    .col {
      flex: 1;
      min-width: min(250px, 100%);
    }

    label {
      display: block;
      margin: 10px 0 5px;
      font-weight: 650;
      font-size: 11px;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.06em;
    }

    input[type=text], input[type=password], input[type=number], input[type=date], input[type=time], input[type=file], select, textarea {
      width: 100%;
      max-width: 100%;
      padding: 10px 11px;
      margin-bottom: 8px;
      border-radius: 8px;
      border: 1px solid var(--border-strong);
      background: var(--surface);
      color: var(--text);
      font-size: 16px;
      line-height: 1.35;
      outline: none;
      transition: border-color 0.16s ease, box-shadow 0.16s ease, background 0.16s ease;
    }

    input[type=file]::file-selector-button {
      margin-right: 10px;
      padding: 7px 10px;
      border: 1px solid var(--border-strong);
      border-radius: 6px;
      color: var(--text);
      background: var(--surface-alt);
      cursor: pointer;
      font-weight: 650;
    }

    input[type=text]::placeholder,
    input[type=password]::placeholder,
    input[type=number]::placeholder,
    textarea::placeholder {
      color: #94a3b8;
    }

    input[type=number] {
      appearance: textfield;
      -moz-appearance: textfield;
      text-align: center;
    }

    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    #teamsCount,
    #groupsCount,
    #teamsPerGroup {
      width: 126px !important;
      min-width: 96px;
      max-width: 126px;
      appearance: auto;
      -moz-appearance: auto;
      text-align: center;
    }

    #teamsCount::-webkit-outer-spin-button,
    #teamsCount::-webkit-inner-spin-button,
    #groupsCount::-webkit-outer-spin-button,
    #groupsCount::-webkit-inner-spin-button,
    #teamsPerGroup::-webkit-outer-spin-button,
    #teamsPerGroup::-webkit-inner-spin-button {
      -webkit-appearance: auto;
      opacity: 1;
    }

    input[type=text]:focus,
    input[type=password]:focus,
    input[type=number]:focus,
    select:focus,
    textarea:focus {
      border-color: var(--primary);
      box-shadow: var(--focus-ring);
      background: #ffffff;
    }

    button {
      background: var(--primary);
      color: #ffffff;
      border: 1px solid var(--primary);
      padding: 10px 14px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 13px;
      font-weight: 650;
      letter-spacing: 0;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      min-height: 38px;
      box-shadow: none;
      transition: transform 0.12s ease, box-shadow 0.12s ease, opacity 0.12s ease, background 0.12s ease, border-color 0.12s ease;
    }

    button.secondary {
      background: #ffffff;
      color: var(--text);
      border-color: var(--border-strong);
    }

    button.danger {
      background: var(--danger);
      border-color: var(--danger);
      color: #ffffff;
    }

    button:hover:not(:disabled) {
      transform: translateY(-1px);
      box-shadow: 0 8px 18px rgba(15, 23, 42, 0.13);
      opacity: 0.98;
    }

    button:active:not(:disabled) {
      transform: translateY(0);
      box-shadow: none;
    }

    button:focus-visible,
    input[type=text]:focus-visible,
    input[type=password]:focus-visible,
    input[type=number]:focus-visible,
    input[type=date]:focus-visible,
    input[type=time]:focus-visible,
    input[type=file]:focus-visible,
    select:focus-visible,
    textarea:focus-visible,
    .bottomNav button:focus-visible,
    .pointsTeamButton:focus-visible,
    .historyCalendarDay:focus-visible {
      outline: none;
      box-shadow: var(--focus-ring);
    }

    button:disabled {
      background: #e5e7eb;
      border-color: #d1d5db;
      color: #94a3b8;
      box-shadow: none;
      cursor: not-allowed;
      opacity: 1;
    }

    table {
      width: 100%;
      max-width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      margin-top: 10px;
      font-size: 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      overflow: hidden;
      background: var(--surface);
    }

    th, td {
      border: 0;
      border-bottom: 1px solid var(--border);
      padding: 9px 8px;
      text-align: center;
      background: var(--surface);
      color: var(--text);
    }

    tr:last-child td {
      border-bottom: 0;
    }

    th {
      background: #0f766e;
      color: #ffffff;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      font-size: 10px;
    }

    .hidden { display: none; }

    .bottomNav {
      position: sticky;
      top: 0;
      z-index: 50;
      display: flex;
      gap: 5px;
      width: min(100%, 1040px);
      max-width: 100%;
      margin: 0 auto 14px;
      padding: 6px;
      overflow-x: auto;
      overflow-y: hidden;
      background: rgba(255, 255, 255, 0.96);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid var(--border);
      border-radius: 8px;
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
      scrollbar-width: thin;
    }

    .bottomNav button {
      flex: 0 0 auto;
      min-width: 104px;
      width: auto;
      background: transparent;
      border: 1px solid transparent;
      color: var(--muted);
      padding: 9px 11px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 12px;
      font-weight: 700;
      box-shadow: none;
      text-transform: none;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .bottomNav button:hover:not(:disabled) {
      background: var(--surface-alt);
      border-color: var(--border);
      color: var(--text);
      transform: none;
      box-shadow: none;
    }

    .bottomNav button.active {
      background: var(--primary-soft);
      color: var(--primary-strong);
      border-color: #99f6e4;
    }

    .bottomNav button:disabled {
      background: transparent;
      color: #b7c1be;
      border-color: transparent;
      cursor: not-allowed;
      opacity: 1;
    }

    body {
      padding-bottom: 28px;
    }

    .pill {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 999px;
      background: var(--primary-soft);
      color: var(--primary-strong);
      font-size: 11px;
      border: 1px solid #99f6e4;
    }

    .primaryNav,
    .tab,
    .hometab {
      display: flex;
      flex-wrap: wrap;
      gap: 5px;
      padding: 5px;
      margin-bottom: 12px;
      border: 1px solid var(--border);
      border-radius: 8px;
      background: var(--surface-alt);
    }

    .primaryNav button,
    .tab button,
    .hometab button {
      background: transparent;
      border: 1px solid transparent;
      color: var(--muted);
      padding: 8px 12px;
      border-radius: 6px;
      box-shadow: none;
      font-size: 13px;
      font-weight: 650;
    }

    .primaryNav button.active,
    .tab button.active,
    .hometab button.active {
      background: var(--surface);
      color: var(--primary-strong);
      border-color: var(--border);
      box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
    }

    .tabcontent,
    .hometabcontent {
      padding-top: 10px;
    }

    .hint {
      font-size: 12px;
      color: var(--muted);
      margin-top: -4px;
      margin-bottom: 11px;
      line-height: 1.45;
    }

    .list {
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 8px;
      background: var(--surface-alt);
      box-shadow: none;
    }

    .listItem {
      display: flex;
      gap: 8px;
      align-items: center;
      justify-content: center;
      padding: 8px 6px;
      border-bottom: 1px solid var(--border);
      position: relative;
      min-height: 42px;
    }

    .listItem:last-child { border-bottom: none; }
    .listItem input { margin-bottom: 0; }

    .playerNameText {
      flex: 1;
      text-align: center;
      font-size: 15px;
      font-weight: 650;
      padding: 0 42px;
      word-break: break-word;
    }

    .removeIconBtn {
      position: absolute;
      right: 6px;
      top: 50%;
      transform: translateY(-50%);
      width: 30px;
      height: 30px;
      min-width: 30px;
      padding: 0;
      border-radius: 8px;
      font-size: 20px;
      line-height: 1;
      font-weight: 700;
      box-shadow: none;
    }

    .smallBtn {
      padding: 6px 9px;
      min-height: 30px;
      font-size: 11px;
      border-radius: 6px;
    }

    .leaderboardPlayerLink {
      appearance: none;
      background: transparent;
      border: 0;
      box-shadow: none;
      color: var(--primary-strong);
      cursor: pointer;
      font: inherit;
      font-weight: 700;
      min-height: 0;
      padding: 5px 6px;
      text-align: center;
      text-decoration: none;
    }

    .leaderboardPlayerLink .teamPlayerTile {
      max-width: 96px;
      margin: 0 auto;
    }

    .leaderboardPlayerLink:hover {
      background: var(--primary-soft);
      box-shadow: none;
      text-decoration: none;
    }

    .leaderboardPlayerLink:focus,
    .leaderboardPlayerLink:focus-visible {
      outline: none;
      box-shadow: none;
    }

    .statGrid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
      gap: 10px;
      margin: 12px 0 14px;
    }

    .statCard {
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 10px;
      background: var(--surface-alt);
      min-width: 0;
    }

    .statValue {
      font-size: 22px;
      font-weight: 800;
      line-height: 1.1;
      color: var(--text);
      overflow-wrap: anywhere;
    }

    .statLabel {
      margin-top: 4px;
      color: var(--muted);
      font-size: 11px;
      font-weight: 750;
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }

    .statSubtle {
      color: var(--muted);
      font-size: 12px;
      line-height: 1.4;
      margin: 4px 0 10px;
    }

    .playerStatsHero {
      display: grid;
      grid-template-columns: minmax(88px, auto) minmax(0, 1fr) minmax(180px, 260px);
      gap: 16px;
      align-items: center;
      padding: 18px;
      border-radius: 12px;
      color: #ffffff;
      background: linear-gradient(135deg, var(--primary-strong), var(--primary));
      box-shadow: 0 12px 26px rgba(15, 118, 110, 0.18);
      overflow: hidden;
    }

    .playerStatsHeroPlayer {
      max-width: 118px;
    }

    .playerStatsHeroPlayer .teamPlayerAvatar {
      width: 72px;
      height: 72px;
      font-size: 22px;
      box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.6), 0 8px 18px rgba(15, 23, 42, 0.2);
    }

    .playerStatsHeroPlayer .teamPlayerName {
      color: #ffffff;
      font-size: 14px;
      font-weight: 850;
      text-shadow: 0 1px 2px rgba(15, 23, 42, 0.18);
    }

    .playerStatsAvatar {
      width: 58px;
      height: 58px;
      display: grid;
      place-items: center;
      border-radius: 50%;
      color: var(--primary-strong);
      background: #ffffff;
      font-size: 24px;
      font-weight: 850;
      box-shadow: 0 6px 16px rgba(15, 23, 42, 0.18);
    }

    .playerStatsName {
      margin: 0;
      color: #ffffff;
      font-size: clamp(21px, 4vw, 29px);
      line-height: 1.1;
    }

    .playerStatsMeta {
      display: flex;
      flex-wrap: wrap;
      gap: 7px;
      margin-top: 8px;
    }

    .playerStatsChip {
      display: inline-flex;
      padding: 4px 9px;
      border: 1px solid rgba(255, 255, 255, 0.28);
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.12);
      color: #ffffff;
      font-size: 11px;
      font-weight: 700;
    }

    .playerWinRate {
      min-width: 0;
    }

    .playerWinRateTop {
      display: flex;
      align-items: baseline;
      justify-content: space-between;
      gap: 8px;
      margin-bottom: 7px;
    }

    .playerWinRateValue {
      font-size: 27px;
      font-weight: 850;
    }

    .playerWinRateLabel {
      font-size: 11px;
      font-weight: 750;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      opacity: 0.85;
    }

    .playerWinRateTrack {
      height: 8px;
      overflow: hidden;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.22);
    }

    .playerWinRateFill {
      height: 100%;
      border-radius: inherit;
      background: #ffffff;
    }

    .playerStatsGrid .statCard {
      border-top: 3px solid var(--primary);
      background: #ffffff;
    }

    .playerStatsGrid .statCard.highlight {
      border-color: #f59e0b;
      background: #fffbeb;
    }

    .playerStatsSection {
      margin-top: 16px;
      padding: 14px;
      border: 1px solid var(--border);
      border-radius: 10px;
      background: var(--surface-alt);
    }

    .playerStatsSectionHeader {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 9px;
    }

    .playerStatsSectionHeader h4 {
      margin: 0;
      font-size: 16px;
    }

    .playerStatsSplit {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
      margin-top: 12px;
    }

    .playerRelationList {
      display: grid;
      gap: 7px;
    }

    .playerRelationRow {
      display: grid;
      grid-template-columns: 26px minmax(86px, 1fr) auto;
      gap: 8px;
      align-items: center;
      padding: 9px 10px;
      border: 1px solid var(--border);
      border-radius: 8px;
      background: #ffffff;
    }

    .playerRelationRank {
      color: var(--muted);
      font-size: 11px;
      font-weight: 800;
    }

    .playerRelationName {
      min-width: 0;
      overflow-wrap: anywhere;
      font-size: 13px;
      font-weight: 700;
    }

    .playerRelationRow .teamPlayerTile {
      min-width: 64px;
      max-width: 104px;
    }

    .playerRelationCount {
      padding: 3px 7px;
      border-radius: 999px;
      color: var(--primary-strong);
      background: var(--primary-soft);
      font-size: 11px;
      font-weight: 800;
    }

    .playerResultBadge {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 999px;
      font-size: 10px;
      font-weight: 800;
      white-space: nowrap;
    }

    .playerResultBadge.champion { color: #92400e; background: #fef3c7; }
    .playerResultBadge.runner { color: #334155; background: #e2e8f0; }
    .playerResultBadge.played { color: #166534; background: #dcfce7; }
    .playerResultBadge.registered { color: var(--muted); background: #f1f5f9; }

    @media (max-width: 700px) {
      .playerStatsHero {
        grid-template-columns: auto minmax(0, 1fr);
      }

      .playerWinRate {
        grid-column: 1 / -1;
      }

      .playerStatsSplit {
        grid-template-columns: 1fr;
      }
    }

    .shuttleSummary {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 10px;
      margin: 12px 0 18px;
    }

    .shuttlePanel {
      padding: 14px;
      border: 1px solid var(--border);
      border-radius: 8px;
      background: var(--surface-alt);
      margin-top: 12px;
    }

    .shuttlePanel h4 {
      margin: 0 0 6px;
    }

    .shuttleStatus {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      white-space: nowrap;
    }

    .shuttleStatus.borrowed {
      background: #fef3c7;
      color: #92400e;
    }

    .shuttleStatus.returned {
      background: #dcfce7;
      color: #166534;
    }

    .knockoutMatches {
      display: grid;
      gap: 12px;
      grid-template-columns: minmax(0, 1fr);
    }

    .knockoutCard {
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 12px;
      background: var(--surface-alt);
      box-shadow: none;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .knockoutHeader {
      font-weight: 700;
      font-size: 12px;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      color: var(--muted);
    }

    .knockoutTeamRow {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
    }

    .knockoutTeamRow span {
      flex: 1;
      font-size: 14px;
    }

    .scoreInput,
    .knockoutTeamRow input[type=number] {
      width: 70px;
      min-width: 70px;
      margin-bottom: 0;
      text-align: center;
    }

    .pointsTeamButton {
      appearance: none;
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      align-items: center;
      justify-content: stretch;
      gap: 8px;
      width: 100%;
      min-height: 0;
      padding: 6px 7px;
      border: 0;
      border-radius: 6px;
      background: transparent;
      box-shadow: none;
      color: var(--primary-strong);
      cursor: pointer;
      font: inherit;
      font-weight: 750;
      text-align: left;
    }

    .teamPhotoDisplay {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: flex-start;
      min-width: 0;
      white-space: normal;
    }

    .teamPhotoDisplay.alignEnd {
      justify-content: flex-end;
      text-align: center;
    }

    .teamPhotoDisplay.alignCenter {
      justify-content: center;
      text-align: center;
    }

    .teamPhotoDisplay.compact {
      gap: 6px;
    }

    .teamPlayerTile {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 5px;
      min-width: 58px;
      max-width: 86px;
      text-align: center;
    }

    .teamPlayerAvatar {
      display: grid;
      place-items: center;
      width: 42px;
      height: 42px;
      overflow: hidden;
      border: 2px solid #ffffff;
      border-radius: 999px;
      background: linear-gradient(135deg, #ccfbf1, #f8fafc);
      box-shadow: 0 0 0 1px rgba(148, 163, 184, 0.55);
      color: var(--primary-strong);
      font-size: 13px;
      font-weight: 900;
      line-height: 1;
      text-transform: uppercase;
    }

    .teamPlayerAvatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .teamPlayerName {
      color: var(--text);
      font-size: 11px;
      font-weight: 800;
      line-height: 1.15;
      overflow-wrap: anywhere;
    }

    .pointsTeamButton .teamPhotoDisplay {
      padding-right: 0;
    }

    .pointsTeamButton:hover,
    .pointsTeamButton[aria-expanded="true"] {
      background: var(--primary-soft);
      color: var(--primary-strong);
      transform: none;
    }

    .pointsTeamButton::after {
      content: "\25BE";
      margin-left: 8px;
      transition: transform 0.15s ease;
    }

    .pointsTeamButton[aria-expanded="true"]::after {
      transform: rotate(180deg);
    }

    .pointsFixtureDetail > td {
      padding: 0;
      background: var(--surface-alt);
    }

    .pointsFixturePanel {
      padding: 12px;
      border-left: 4px solid var(--primary);
      text-align: left;
    }

    .pointsFixturePanelHeader {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      font-weight: 750;
    }

    .pointsFixtureTable {
      margin: 8px 0 0;
    }

    .pointsFixtureTable .scoreInput {
      width: 66px;
      min-width: 66px;
    }

    .pointsFixtureTable .smallBtn {
      margin: 0;
      white-space: nowrap;
    }

    .knockoutFooter {
      font-size: 12px;
      color: var(--muted);
    }

    .finalAwards {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
    }

    .finalAward {
      display: flex;
      align-items: flex-start;
      gap: 14px;
      min-width: 0;
      padding: 14px 16px;
      border: 1px solid var(--border);
      border-radius: 10px;
      background: var(--surface);
    }

    .finalAward.winner {
      border-color: #f5cf66;
      background: linear-gradient(135deg, #fffbeb, #fef3c7);
    }

    .finalAward.runnerUp {
      border-color: #cbd5e1;
      background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    }

    .finalAwardIcon {
      flex: 0 0 54px;
      width: 54px;
      height: 54px;
      display: grid;
      place-items: center;
    }

    .finalAwardIcon.trophy {
      font-size: 43px;
      line-height: 1;
      filter: drop-shadow(0 4px 5px rgba(146, 64, 14, 0.18));
    }

    .finalAwardIcon.plate {
      position: relative;
      border: 3px solid #94a3b8;
      border-radius: 50%;
      background: radial-gradient(circle, #ffffff 0 30%, #cbd5e1 32% 47%, #f8fafc 49% 63%, #94a3b8 65% 68%, #e2e8f0 70%);
      box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.75), 0 5px 10px rgba(71, 85, 105, 0.18);
    }

    .finalAwardLabel {
      color: var(--muted);
      font-size: 10px;
      font-weight: 800;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .finalAwardName {
      margin-top: 3px;
      color: var(--text);
      font-size: clamp(17px, 3vw, 23px);
      font-weight: 850;
      line-height: 1.15;
      overflow-wrap: anywhere;
    }

    .finalAward.winner .finalAwardName {
      color: #92400e;
    }

    .awardPlayers {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 10px;
    }

    .awardPlayer {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      min-width: 76px;
      max-width: 104px;
      text-align: center;
    }

    .awardPlayerPhoto {
      width: 58px;
      height: 58px;
      border: 2px solid #ffffff;
      box-shadow: 0 0 0 1px rgba(148, 163, 184, 0.5);
      font-size: 18px;
    }

    .awardPlayerName {
      color: var(--text);
      font-size: 12px;
      font-weight: 800;
      line-height: 1.2;
      overflow-wrap: anywhere;
    }

    @media (max-width: 600px) {
      .finalAwards {
        grid-template-columns: 1fr;
      }

      .playersManager {
        grid-template-columns: 1fr;
      }

      .playerCard {
        grid-template-columns: 48px minmax(0, 1fr);
      }

      .playerAvatar {
        width: 48px;
        height: 48px;
      }

      .playerCardActions {
        grid-column: 1 / -1;
        justify-content: flex-end;
      }
    }

    body.authLocked {
      min-height: 100svh;
      padding: clamp(14px, 3vw, 28px);
      align-items: center;
      justify-content: center;
      background:
        linear-gradient(135deg, rgba(15, 118, 110, 0.12), rgba(245, 158, 11, 0.08) 42%, rgba(248, 250, 252, 0) 74%),
        var(--bg);
    }

    body.authLocked .skipLink[href="#bottomNav"] {
      display: none;
    }

    body.authLocked #authSection {
      width: min(100%, 1040px);
      margin: 0 auto;
      padding: 0;
      display: grid;
      grid-template-columns: minmax(0, 1.04fr) minmax(320px, 0.96fr);
      border: 1px solid #c4d8d4;
      border-radius: 14px;
      overflow: hidden;
      box-shadow: 0 24px 70px rgba(15, 23, 42, 0.16);
    }

    .authBrandPanel {
      min-height: 620px;
      padding: clamp(28px, 4vw, 46px);
      color: #ecfeff;
      background:
        linear-gradient(150deg, rgba(15, 94, 89, 0.98), rgba(15, 118, 110, 0.9) 58%, rgba(12, 74, 110, 0.92)),
        #0f4f4a;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 0;
      position: relative;
      overflow: hidden;
    }

    .authIllustration {
      position: relative;
      z-index: 1;
    }

    .authEyebrow {
      color: rgba(236, 254, 255, 0.78);
      font-size: 11px;
      font-weight: 850;
      letter-spacing: 0.12em;
      text-transform: uppercase;
    }

    .authIllustration {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
    }

    .authBrandImage {
      display: block;
      width: min(92%, 430px);
      height: auto;
      max-height: 430px;
      object-fit: contain;
      filter: drop-shadow(0 20px 24px rgba(0, 0, 0, 0.28));
    }

    .authFormPanel {
      min-width: 0;
      padding: clamp(28px, 4vw, 48px);
      background: #ffffff;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .authFormHeader {
      margin-bottom: 22px;
    }

    .authFormHeader .authEyebrow {
      color: var(--primary-strong);
    }

    .authFormHeader h3 {
      margin: 7px 0 8px;
      font-size: clamp(25px, 4vw, 34px);
      font-weight: 880;
    }

    .authFormHeader p {
      margin: 0;
      color: var(--muted);
      font-size: 14px;
      line-height: 1.55;
    }

    #authSection .authPanel h4 {
      margin: 0 0 14px;
      color: var(--text);
      font-size: 15px;
      font-weight: 850;
    }

    #authSection label {
      margin-top: 12px;
    }

    #authSection input[type=text],
    #authSection input[type=password] {
      min-height: 46px;
      margin-bottom: 12px;
      background: #f8fafc;
    }

    #authSection input[type=text]:focus,
    #authSection input[type=password]:focus {
      background: #ffffff;
    }

    #loginBtn,
    #resetMyPasswordBtn {
      width: 100%;
      min-height: 46px;
      margin-top: 4px;
      font-size: 14px;
      font-weight: 800;
    }

    body.authLocked > h1,
    body.authLocked > .subTitle,
    body.authLocked > .bottomNav,
    body.authLocked > .section:not(#authSection) {
      display: none !important;
    }

    #authSection.hidden,
    .adminOnly.hidden {
      display: none !important;
    }

    .authStatus {
      margin-top: 10px;
      font-size: 13px;
      color: var(--muted);
      min-height: 18px;
    }

    .authStatus.error { color: var(--danger); }
    .authStatus.success { color: var(--accent); }

    @media (min-width: 720px) {
      .knockoutMatches {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (prefers-reduced-motion: reduce) {
      *,
      *::before,
      *::after {
        scroll-behavior: auto !important;
        transition-duration: 0.01ms !important;
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
      }
    }

    @media (min-width: 980px) {
      body {
        padding: 18px 24px 34px 248px;
      }

      body.authLocked {
        padding: 24px;
      }

      h1,
      .subTitle,
      .section {
        margin-left: auto;
        margin-right: auto;
      }

      .bottomNav {
        position: fixed;
        top: 16px;
        left: 16px;
        bottom: 16px;
        width: 208px;
        margin: 0;
        padding: 12px;
        flex-direction: column;
        align-items: stretch;
        overflow-x: hidden;
        overflow-y: auto;
      }

      .bottomNav::before {
        content: 'Navigation';
        display: block;
        padding: 3px 4px 10px;
        color: var(--muted);
        font-size: 11px;
        font-weight: 750;
        letter-spacing: 0.08em;
        text-transform: uppercase;
      }

      .bottomNav button {
        width: 100%;
        min-width: 0;
        justify-content: flex-start;
        padding: 10px 12px;
        font-size: 13px;
      }

      .bottomNav button.active {
        background: var(--primary-soft);
        border-color: transparent;
        box-shadow: inset 3px 0 0 var(--primary);
      }
    }

    @media (max-width: 600px) {
      body {
        padding: 8px 6px 20px;
      }

      body.authLocked {
        padding: 10px;
        align-items: stretch;
        justify-content: flex-start;
      }

      h1 {
        font-size: 22px;
      }

      .section {
        padding: 12px 10px;
        border-radius: 8px;
        width: 100%;
      }

      h3 {
        font-size: 18px;
      }

      .row {
        flex-direction: column;
        align-items: stretch;
      }

      .col {
        min-width: 100%;
      }

      button {
        width: 100%;
        justify-content: center;
      }

      .row > button,
      .row > .col > button,
      .row > div > button {
        width: 100%;
        margin-top: 4px;
      }

      table {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }

      th, td {
        white-space: nowrap;
        padding: 8px 7px;
      }

      .bottomNav {
        width: 100%;
        margin-bottom: 12px;
        border-radius: 8px;
        padding: 5px;
      }

      .bottomNav button {
        width: auto;
        min-width: 104px;
        justify-content: center;
        font-size: 12px;
        padding: 9px 10px;
        margin-top: 0;
      }

      input[type=number] {
        width: 100% !important;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) {
        overflow-x: visible;
        padding-bottom: 2px;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable {
        display: block;
        min-width: 0;
        border: 0;
        border-radius: 0;
        background: transparent;
        overflow: visible;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable thead {
        display: none;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable tbody {
        display: grid;
        gap: 8px;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable tr {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 46px 10px 46px minmax(0, 1fr);
        align-items: center;
        gap: 6px;
        padding: 8px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: var(--surface);
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable td {
        display: flex;
        min-width: 0;
        padding: 0;
        border: 0;
        background: transparent;
        white-space: normal;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable td:nth-child(1) {
        display: none;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable td:nth-child(2),
      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable td:nth-child(6) {
        align-items: center;
        font-weight: 650;
        line-height: 1.15;
        overflow-wrap: anywhere;
        word-break: break-word;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable td:nth-child(2) {
        grid-column: 1;
        justify-content: flex-end;
        text-align: right;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable td:nth-child(6) {
        grid-column: 5;
        justify-content: flex-start;
        text-align: left;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable td:nth-child(3),
      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable td:nth-child(5) {
        align-items: center;
        justify-content: center;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable td:nth-child(3) {
        grid-column: 2;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable td:nth-child(5) {
        grid-column: 4;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable td:nth-child(4) {
        grid-column: 3;
        align-items: center;
        justify-content: center;
        color: var(--muted);
        font-size: 0;
        font-weight: 800;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable td:nth-child(4)::before {
        content: 'vs';
        font-size: 11px;
        line-height: 1;
        letter-spacing: 0.05em;
        text-transform: uppercase;
      }

      :is(#scheduleOutput, #playoffBracketOutput, #finalMatchOutput) .scheduleTable .scoreInput {
        width: 44px !important;
        min-width: 44px;
        max-width: 44px;
        padding-left: 4px;
        padding-right: 4px;
      }

      :is(#playoffBracketOutput, #finalMatchOutput) .scheduleTable tr {
        position: relative;
        padding-top: 30px;
      }

      :is(#playoffBracketOutput, #finalMatchOutput) .scheduleTable td:nth-child(1) {
        display: block;
        position: absolute;
        top: 8px;
        left: 8px;
        color: var(--primary-strong);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
      }

      .scoreInput,
      .knockoutTeamRow input[type=number] {
        width: 72px !important;
        min-width: 72px;
        max-width: 72px;
      }

      .hometab,
      .tab {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }
    }

    @media (max-width: 380px) {
      .bottomNav button {
        min-width: 96px;
        font-size: 11px;
        padding-left: 8px;
        padding-right: 8px;
      }

      th, td {
        padding: 7px 6px;
      }
    }

    @media (max-width: 760px) {
      body.authLocked #authSection {
        grid-template-columns: 1fr;
        border-radius: 12px;
      }

      .authBrandPanel {
        min-height: 360px;
        padding: 24px;
      }

      .authFormPanel {
        padding: 24px;
      }
    }

    @media (max-width: 430px) {
      .authBrandPanel {
        min-height: 310px;
      }
    }
  </style>
</head>
<body>
<script>
  // PHP-hosted persistence + app login:
  // Domain data is read/written through the same-host token-protected PHP API.
  (function bootstrapServerBackedStorage() {
    const params = new URLSearchParams(window.location.search);
    const forcedLocalMode = params.get('storage') === 'local'
      || params.get('mode') === 'local';
    const localMobileMode = forcedLocalMode
      || !['http:', 'https:'].includes(window.location.protocol)
      || window.location.href.startsWith('file:///android_asset/');

    if (localMobileMode) {
      const localSession = {
        token: 'local-mobile',
        username: 'local',
        displayName: 'Local Device',
        isAdmin: false,
        mustResetPassword: false,
      };
      window.btAuth = {
        getSession: () => localSession,
        setSession: () => {},
        clearSession: () => {},
        consumeSessionNotice: () => '',
        hydrateFromDatabase: () => true,
        postRpc: async () => new Response(JSON.stringify([]), {
          status: 200,
          headers: { 'Content-Type': 'application/json' },
        }),
        patchMatchScore: () => {},
        syncTournamentScores: () => {},
        syncScoreRows: () => {},
        authToken: () => localSession.token,
        startSessionActivityWatch: () => {},
        login: async () => localSession,
        logout: async () => {},
      };
      document.body.classList.remove('authLocked');
      document.documentElement.dataset.storageMode = 'local';
      return;
    }

    document.body.classList.add('authLocked');

    const APP_KEY_PREFIX = 'bt_';
    const TOURNAMENT_PREFIX = 'bt_tournament_v1_';
    const PLAYER_LIST_PREFIX = 'bt_playerlist_v1_';
    const apiBaseUrl = (() => {
      const current = new URL(window.location.href);
      const path = current.pathname || '/';
      let directory;
      if (path.endsWith('/')) {
        directory = path;
      } else {
        const lastSegment = path.slice(path.lastIndexOf('/') + 1);
        directory = lastSegment.includes('.')
          ? path.slice(0, path.lastIndexOf('/') + 1)
          : `${path}/`;
      }
      return `${current.origin}${directory}api.php`;
    })();
    window.btApiUrl = apiBaseUrl;
    const rpcUrl = (path) => {
      const url = new URL(apiBaseUrl);
      url.searchParams.set('action', path);
      return url.toString();
    };
    const nativeSetItem = Storage.prototype.setItem;
    const nativeRemoveItem = Storage.prototype.removeItem;
    const sessionKey = 'bt_auth_session_v1';
    const sessionNoticeKey = 'bt_auth_notice_v1';
    const sessionIdleTimeoutMs = 2 * 60 * 1000;
    const sessionRefreshMs = 1000;
    const sessionActivityEvents = ['click', 'keydown', 'input', 'pointerdown', 'touchstart', 'scroll'];
    let lastActivityAt = Date.now();
    let lastRefreshAt = 0;
    let idleTimer = null;
    const writeQueues = new Map();
    const activeWrites = new Set();
    let activityWatchStarted = false;
    let sessionRefreshInFlight = false;
    let expiringSession = false;

    function isAppKey(key) { return typeof key === 'string' && key.startsWith(APP_KEY_PREFIX); }
    function isTournamentKey(key) { return typeof key === 'string' && key.startsWith(TOURNAMENT_PREFIX); }
    function isPlayerListKey(key) { return typeof key === 'string' && key.startsWith(PLAYER_LIST_PREFIX); }

    function clearLocalAppData() {
      const keys = [];
      for (let index = 0; index < localStorage.length; index += 1) {
        const key = localStorage.key(index);
        if (isAppKey(key)) keys.push(key);
      }
      keys.forEach(key => nativeRemoveItem.call(localStorage, key));
    }

    function getSession() {
      try { return JSON.parse(sessionStorage.getItem(sessionKey) || 'null'); }
      catch { return null; }
    }

    function setSession(session) {
      sessionStorage.setItem(sessionKey, JSON.stringify(session));
    }

    function clearSession() {
      stopSessionActivityWatch();
      sessionStorage.removeItem(sessionKey);
    }

    function setSessionNotice(message) {
      if (message) sessionStorage.setItem(sessionNoticeKey, message);
    }

    function consumeSessionNotice() {
      const message = sessionStorage.getItem(sessionNoticeKey) || '';
      sessionStorage.removeItem(sessionNoticeKey);
      return message;
    }

    function authToken() {
      return getSession()?.token || '';
    }

    function expireLocalSession(message, notifyServer = true) {
      if (expiringSession) return;
      expiringSession = true;
      const token = authToken();
      flushPendingDatabaseWrites();
      clearSession();
      setSessionNotice(message);
      if (notifyServer && token) {
        const payload = JSON.stringify({ auth_token: token });
        try {
          navigator.sendBeacon(rpcUrl('logout_user'), new Blob([payload], { type: 'application/json' }));
        } catch {
          fetch(rpcUrl('logout_user'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: payload,
            keepalive: true,
          }).catch(() => {});
        }
      }
      location.reload();
    }

    async function handleExpiredResponse(res) {
      if (!res || res.ok || !authToken()) return res;
      try {
        const text = await res.clone().text();
        if (text.includes('Invalid or expired session')) {
          expireLocalSession('Session expired after 2 minutes of inactivity. Please log in again.', false);
        }
      } catch (error) {
        console.warn('Could not inspect auth response:', error);
      }
      return res;
    }

    function sendJson(url, options) {
      const headers = { 'Content-Type': 'application/json', ...(options?.headers || {}) };
      return fetch(url, {
        cache: 'no-store',
        credentials: 'same-origin',
        ...options,
        headers,
      }).then(handleExpiredResponse).catch((error) => {
        console.warn('Database sync failed:', error);
      });
    }

    function sendTrackedJson(url, options) {
      const write = sendJson(url, options);
      activeWrites.add(write);
      write.finally(() => activeWrites.delete(write));
      return write;
    }

    async function postRpc(path, payload) {
      const url = rpcUrl(path);
      let res;
      try {
        res = await fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload || {}),
          cache: 'no-store',
          credentials: 'same-origin',
        });
      } catch (error) {
        throw new Error(`Network error while calling ${url}: ${error?.message || error}`);
      }
      await handleExpiredResponse(res);
      return res;
    }

    function scheduleIdleTimeout() {
      window.clearTimeout(idleTimer);
      if (!authToken()) return;
      const elapsed = Date.now() - lastActivityAt;
      const delay = Math.max(0, sessionIdleTimeoutMs - elapsed);
      idleTimer = window.setTimeout(() => {
        if (Date.now() - lastActivityAt >= sessionIdleTimeoutMs) {
          expireLocalSession('Session expired after 2 minutes of inactivity. Please log in again.');
          return;
        }
        scheduleIdleTimeout();
      }, delay + 250);
    }

    async function refreshSession() {
      const session = getSession();
      if (!session?.token || sessionRefreshInFlight) return;
      if (Date.now() - lastActivityAt >= sessionIdleTimeoutMs) {
        expireLocalSession('Session expired after 2 minutes of inactivity. Please log in again.');
        return;
      }

      sessionRefreshInFlight = true;
      try {
        const res = await postRpc('refresh_session', { auth_token: session.token });
        if (res.ok) {
          const refreshed = await res.json().catch(() => null);
          if (refreshed?.expiresAt) {
            setSession({ ...session, expiresAt: refreshed.expiresAt });
          }
        }
      } finally {
        sessionRefreshInFlight = false;
      }
    }

    function recordSessionActivity() {
      if (!authToken()) return;
      lastActivityAt = Date.now();
      scheduleIdleTimeout();
      if (lastActivityAt - lastRefreshAt >= sessionRefreshMs) {
        lastRefreshAt = lastActivityAt;
        refreshSession();
      }
    }

    function startSessionActivityWatch() {
      if (!authToken()) return;
      lastActivityAt = Date.now();
      scheduleIdleTimeout();
      if (!activityWatchStarted) {
        sessionActivityEvents.forEach((eventName) => {
          window.addEventListener(eventName, recordSessionActivity, { passive: true });
        });
        activityWatchStarted = true;
      }
      lastRefreshAt = Date.now();
      refreshSession();
    }

    function stopSessionActivityWatch() {
      window.clearTimeout(idleTimer);
      idleTimer = null;
      lastRefreshAt = 0;
    }

    function queueDatabaseWrite(queueKey, writeOperation) {
      const queue = writeQueues.get(queueKey) || {
        running: false,
        dirty: false,
        operation: null,
      };
      queue.operation = writeOperation;
      queue.dirty = true;
      writeQueues.set(queueKey, queue);
      if (!queue.running) runDatabaseWriteQueue(queueKey);
      return true;
    }

    async function runDatabaseWriteQueue(queueKey) {
      const queue = writeQueues.get(queueKey);
      if (!queue || queue.running) return;

      queue.running = true;
      try {
        while (queue.dirty && queue.operation) {
          queue.dirty = false;
          await queue.operation();
        }
      } catch (error) {
        console.warn('Database sync failed:', error);
      } finally {
        queue.running = false;
        if (queue.dirty) {
          runDatabaseWriteQueue(queueKey);
        } else {
          writeQueues.delete(queueKey);
        }
      }
    }

    async function waitForQueuedDatabaseWrites() {
      const timeout = new Promise(resolve => window.setTimeout(resolve, 10000));
      const drain = (async () => {
        for (let attempt = 0; attempt < 30; attempt++) {
          writeQueues.forEach((queue, key) => {
            if (!queue.running) runDatabaseWriteQueue(key);
          });
          if (writeQueues.size === 0 && activeWrites.size === 0) return;
          await Promise.allSettled([...activeWrites]);
          await new Promise(resolve => window.setTimeout(resolve, 25));
        }
      })();
      await Promise.race([drain, timeout]);
    }

    function hydrateFromDatabase(token) {
      if (!token) return false;
      try {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', rpcUrl('export_app_state'), false);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(JSON.stringify({ auth_token: token }));
        if (xhr.status >= 200 && xhr.status < 300) {
          const items = JSON.parse(xhr.responseText || '{}') || {};
          clearLocalAppData();
          Object.keys(items).forEach((key) => {
            if (isAppKey(key)) nativeSetItem.call(localStorage, key, items[key]);
          });
          return true;
        }
        if ((xhr.responseText || '').includes('Invalid or expired session')) {
          setSessionNotice('Session expired after 2 minutes of inactivity. Please log in again.');
        }
        clearSession();
      } catch (error) {
        console.warn('Could not hydrate from normalized database APIs.', error);
      }
      return false;
    }

    function normalizeScoreForSync(value) {
      if (value === null || value === undefined || value === '') return null;
      const score = Number(value);
      if (!Number.isFinite(score)) return null;
      return Math.trunc(score);
    }

    function collectTournamentScorePayload(payload) {
      if (!payload || typeof payload !== 'object' || !payload.id) {
        return { tournament_id: '', scores: [] };
      }

      const rowsById = new Map();
      const addMatch = (match) => {
        if (!match || typeof match !== 'object') return;
        const matchId = String(match.id || '').trim();
        if (!matchId) return;
        const score1 = normalizeScoreForSync(match.score1);
        const score2 = normalizeScoreForSync(match.score2);
        if (score1 === null && score2 === null) return;
        const existing = rowsById.get(matchId) || { match_id: matchId, score1: null, score2: null };
        if (score1 !== null) existing.score1 = score1;
        if (score2 !== null) existing.score2 = score2;
        rowsById.set(matchId, existing);
      };

      if (Array.isArray(payload.matches)) {
        payload.matches.forEach(addMatch);
      }
      addMatch(payload.finalMatch);
      const knockout = payload.knockout;
      if (knockout && typeof knockout === 'object') {
        ['semifinal1', 'semifinal2', 'qualifier1', 'eliminator', 'qualifier2', 'final'].forEach((key) => {
          addMatch(knockout[key]);
        });
      }

      return { tournament_id: String(payload.id), scores: [...rowsById.values()] };
    }

    function saveDomainKey(key, value) {
      const token = authToken();
      if (!token) return false;

      if (isTournamentKey(key)) {
        try {
          const payload = JSON.parse(value);
          const scorePayload = collectTournamentScorePayload(payload);
          queueDatabaseWrite(`domain:${key}`, () => {
            return sendTrackedJson(rpcUrl('save_tournament'), {
              method: 'POST',
              body: JSON.stringify({ auth_token: token, payload }),
            }).then((res) => {
              if (!scorePayload.scores.length) return res;
              return sendTrackedJson(rpcUrl('save_match_scores'), {
                method: 'POST',
                body: JSON.stringify({ auth_token: token, ...scorePayload }),
              });
            });
          });
        } catch (error) {
          console.warn('Could not save tournament to normalized API:', error);
        }
        return true;
      }

      if (isPlayerListKey(key)) {
        try {
          const payload = JSON.parse(value);
          queueDatabaseWrite(`domain:${key}`, () => {
            return sendTrackedJson(rpcUrl('save_player_list'), {
              method: 'POST',
              body: JSON.stringify({ auth_token: token, storage_key: key, payload }),
            });
          });
        } catch (error) {
          console.warn('Could not save player list to normalized API:', error);
        }
        return true;
      }

      return false;
    }

    function patchMatchScore(tournamentId, matchId, scoreSide, scoreValue) {
      const token = authToken();
      if (!token || !tournamentId || !matchId || (scoreSide !== 1 && scoreSide !== 2)) return false;
      const patchKey = `${tournamentId}:${matchId}:${scoreSide}`;
      return queueDatabaseWrite(`score:${patchKey}`, () => sendTrackedJson(rpcUrl('patch_match_score'), {
        method: 'POST',
        body: JSON.stringify({
          auth_token: token,
          tournament_id: tournamentId,
          match_id: String(matchId),
          score_side: scoreSide,
          score_value: scoreValue === '' || scoreValue === undefined ? null : scoreValue,
        }),
      }));
    }

    function syncTournamentScores(payload) {
      const token = authToken();
      const scorePayload = collectTournamentScorePayload(payload);
      if (!token || !scorePayload.tournament_id || !scorePayload.scores.length) return false;
      return queueDatabaseWrite(`scores:${scorePayload.tournament_id}`, () => sendTrackedJson(rpcUrl('save_match_scores'), {
        method: 'POST',
        body: JSON.stringify({ auth_token: token, ...scorePayload }),
      }));
    }

    function syncScoreRows(tournamentId, scores) {
      const token = authToken();
      const cleaned = Array.isArray(scores) ? scores.filter(row => row?.match_id && (row.score1 !== null || row.score2 !== null)) : [];
      if (!token || !tournamentId || !cleaned.length) return false;
      return queueDatabaseWrite(`visible-scores:${tournamentId}`, () => sendTrackedJson(rpcUrl('save_match_scores'), {
        method: 'POST',
        body: JSON.stringify({ auth_token: token, tournament_id: String(tournamentId), scores: cleaned }),
      }));
    }

    function flushPendingDatabaseWrites() {
      return waitForQueuedDatabaseWrites();
    }

    function deleteDomainKey(key) {
      const token = authToken();
      if (!token) return false;

      if (isTournamentKey(key)) {
        queueDatabaseWrite(`domain:${key}`, () => sendTrackedJson(rpcUrl('delete_tournament'), {
          method: 'POST',
          body: JSON.stringify({ auth_token: token, tournament_id: key.replace(TOURNAMENT_PREFIX, '') }),
        }));
        return true;
      }

      if (isPlayerListKey(key)) {
        queueDatabaseWrite(`domain:${key}`, () => sendTrackedJson(rpcUrl('delete_player_list'), {
          method: 'POST',
          body: JSON.stringify({ auth_token: token, player_list_id: key.replace(PLAYER_LIST_PREFIX, '') }),
        }));
        return true;
      }

      return false;
    }

    window.btAuth = {
      getSession,
      setSession,
      clearSession,
      consumeSessionNotice,
      hydrateFromDatabase,
      postRpc,
      patchMatchScore,
      syncTournamentScores,
      syncScoreRows,
      authToken,
      startSessionActivityWatch,
      flushPendingDatabaseWrites,
      async login(username, password) {
        const res = await postRpc('login_user', { username, password });
        const responseText = await res.text();
        if (!res.ok) throw new Error(responseText || `API request failed with HTTP ${res.status}.`);
        let session;
        try {
          session = JSON.parse(responseText);
        } catch {
          const contentType = res.headers.get('content-type') || 'unknown content type';
          throw new Error(`Invalid API response (HTTP ${res.status}, ${contentType}). Open health.php and confirm it shows READY.`);
        }
        if (!session?.token) {
          throw new Error('The login API returned no session token. Open health.php and confirm it shows READY.');
        }
        setSession(session);
        startSessionActivityWatch();
        if (!session.mustResetPassword) hydrateFromDatabase(session.token);
        return session;
      },
      async logout() {
        const token = authToken();
        await flushPendingDatabaseWrites();
        if (token) await postRpc('logout_user', { auth_token: token });
        clearSession();
        location.reload();
      },
    };

    window.addEventListener('pagehide', () => {
      flushPendingDatabaseWrites();
    });
    window.addEventListener('beforeunload', () => {
      flushPendingDatabaseWrites();
    });

    const session = getSession();
    if (session?.token && !session.mustResetPassword && hydrateFromDatabase(session.token)) {
      document.body.classList.remove('authLocked');
    }

    Storage.prototype.setItem = function patchedSetItem(key, value) {
      nativeSetItem.call(this, key, value);
      if (this !== localStorage || !isAppKey(key)) return;
      if (saveDomainKey(key, String(value))) return;
      const token = authToken();
      if (!token) return;
      queueDatabaseWrite(`setting:${key}`, () => sendTrackedJson(rpcUrl('save_app_setting'), {
        method: 'POST',
        body: JSON.stringify({ auth_token: token, storage_key: key, storage_value: String(value) }),
      }));
    };

    Storage.prototype.removeItem = function patchedRemoveItem(key) {
      nativeRemoveItem.call(this, key);
      if (this !== localStorage || !isAppKey(key)) return;
      if (deleteDomainKey(key)) return;
      const token = authToken();
      if (!token) return;
      queueDatabaseWrite(`setting:${key}`, () => sendTrackedJson(rpcUrl('delete_app_setting'), {
        method: 'POST',
        body: JSON.stringify({ auth_token: token, storage_key: key }),
      }));
    };
  })();
</script>

<a class="skipLink" href="#authSection">Skip to login</a>
<a class="skipLink" href="#bottomNav">Skip to app navigation</a>

<h1 id="pageTitle">Badminton Tournament Manager</h1>
<div class="subTitle">Run tournaments, scoring, standings, finals, and history from one organized workspace.</div>
<div class="section hidden" id="sessionBar">
  <div class="accountBar">
    <div class="accountPill">
      <span class="accountLabel">Signed in</span>
      <span class="accountName" id="signedInUser">-</span>
      <button id="sessionLogoutBtn" class="secondary" type="button">Logout</button>
    </div>
  </div>
</div>


<div class="section" id="authSection" aria-labelledby="authHeading">
  <div class="authBrandPanel">
    <div class="authIllustration" aria-hidden="true">
      <img class="authBrandImage" src="bs-optimized.png" alt="" loading="eager" decoding="async" />
    </div>
  </div>

  <div class="authFormPanel">
    <div class="authFormHeader">
      <div class="authEyebrow">Welcome back</div>
      <h3 id="authHeading">Account Access</h3>
      <p>Sign in to continue managing tournaments and score updates.</p>
    </div>

    <div id="loginPanel" class="authPanel">
      <h4>Login</h4>
      <label for="loginUsername">Username</label>
      <input id="loginUsername" type="text" autocomplete="username" placeholder="Username" />
      <label for="loginPassword">Password</label>
      <input id="loginPassword" type="password" autocomplete="current-password" placeholder="Password" />
      <button id="loginBtn">Login</button>
      <div id="loginStatus" class="authStatus"></div>
    </div>

    <div id="resetPasswordPanel" class="authPanel hidden">
      <h4>Reset Password</h4>
      <div class="hint">You must set a new password before using the app.</div>
      <label for="currentPasswordInput">Current / Temporary Password</label>
      <input id="currentPasswordInput" type="password" autocomplete="current-password" />
      <label for="newPasswordInput">New Password</label>
      <input id="newPasswordInput" type="password" autocomplete="new-password" />
      <label for="confirmPasswordInput">Confirm New Password</label>
      <input id="confirmPasswordInput" type="password" autocomplete="new-password" />
      <button id="resetMyPasswordBtn">Reset Password</button>
      <div id="resetPasswordStatus" class="authStatus"></div>
    </div>
  </div>
</div>
<!-- Top tab bar (centered feature navigation) -->
<div class="bottomNav" id="bottomNav" role="tablist" aria-label="Tournament features" aria-orientation="horizontal">
  <button id="tabTournament" class="bottomNavBtn active" data-view="tournament" role="tab" aria-controls="viewTournament" aria-selected="true">Tournament</button>
  <button id="tabPlayers" class="bottomNavBtn" data-view="players" role="tab" aria-controls="viewPlayers" aria-selected="false">Players</button>
  <button id="tabShuttles" class="bottomNavBtn" data-view="shuttles" role="tab" aria-controls="viewShuttles" aria-selected="false">Shuttle Management</button>
  <button id="tabSchedule" class="bottomNavBtn" data-view="schedule" role="tab" aria-controls="viewSchedule" aria-selected="false" disabled>Schedule</button>
  <button id="tabPoints" class="bottomNavBtn" data-view="points" role="tab" aria-controls="viewPoints" aria-selected="false" disabled>Points</button>
  <button id="tabUsers" class="bottomNavBtn" data-view="users" role="tab" aria-controls="usersAdminSection" aria-selected="false" disabled>Users</button>
  <button id="tabLeaderboard" class="bottomNavBtn" data-view="leaderboard" role="tab" aria-controls="viewLeaderboard" aria-selected="false">Leaderboard</button>
  <button id="tabStats" class="bottomNavBtn" data-view="stats" role="tab" aria-controls="viewStats" aria-selected="false">Player Statistics</button>
  <button id="tabHistory" class="bottomNavBtn" data-view="history" role="tab" aria-controls="viewHistory" aria-selected="false">History</button>
</div>

<!-- Unified, feature-based screens (one feature per tab) -->

<!-- Tournament tab (create/load + tournament config) -->
<div class="section featureView" id="viewTournament" role="tabpanel" aria-labelledby="tabTournament">
  <div class="workspaceHeader">
    <div class="workspaceHeaderIcon" aria-hidden="true">&#127967;</div>
    <div><h3>Tournament</h3><div class="hint">Create or load a tournament, then configure format and generate the schedule.</div></div>
  </div>

  <div class="tournamentFlow" id="tournamentFlow">
    <div class="tournamentChoiceGrid" id="tournamentActionChooser" aria-label="Tournament actions">
      <button id="startCreateTournamentBtn" class="tournamentChoiceCard" type="button">
        <span class="tournamentChoiceIcon createIcon" aria-hidden="true"></span>
        <span>
          <span class="tournamentChoiceTitle">Create Tournament</span>
          <span class="tournamentChoiceText">Start a new tournament and configure the format, teams, and schedule.</span>
        </span>
      </button>
      <button id="startLoadTournamentBtn" class="tournamentChoiceCard" type="button">
        <span class="tournamentChoiceIcon loadIcon" aria-hidden="true"></span>
        <span>
          <span class="tournamentChoiceTitle">Load Tournament</span>
          <span class="tournamentChoiceText">Open a saved tournament and continue scoring or editing setup.</span>
        </span>
      </button>
    </div>

    <div class="tournamentFlowPanel hidden" id="createTournamentPanel">
      <div class="tournamentFlowHeader">
        <div>
          <h4>Create Tournament</h4>
          <div class="hint">Enter a name to enable tournament creation.</div>
        </div>
        <button id="tournamentCreateBackBtn" class="secondary tournamentBackBtn" type="button">&#8592; Tournament</button>
      </div>
      <label for="newTournamentName">Tournament Name</label>
      <input id="newTournamentName" type="text" placeholder="e.g., Summer Smash 2026" />
      <button id="createTournamentBtn" type="button" disabled>Create Tournament</button>
    </div>

    <div class="tournamentFlowPanel hidden" id="loadTournamentPanel">
      <div class="tournamentFlowHeader">
        <div>
          <h4>Load Tournament</h4>
          <div class="hint">Choose a saved tournament, then load it.</div>
        </div>
        <button id="tournamentLoadBackBtn" class="secondary tournamentBackBtn" type="button">&#8592; Tournament</button>
      </div>
      <label for="loadTournamentSelect">Saved Tournaments</label>
      <select id="loadTournamentSelect">
        <option value="">-- Select --</option>
      </select>
      <div class="row">
        <button id="loadTournamentBtn" type="button" disabled>Load</button>
        <button id="deleteTournamentBtn" class="danger" type="button" disabled>Delete</button>
      </div>
    </div>
  </div>

  <div id="tournamentSetupPanel" class="hidden">
  <div class="tournamentFlowHeader">
    <div>
      <h4>Current Tournament</h4>
      <div class="hint"><span id="currentTournamentName">-</span></div>
    </div>
    <div class="row" style="justify-content: flex-end;">
      <button id="closeTournamentBtn" class="secondary" type="button">&#8592; Tournament</button>
      <button id="resetTournamentBtn" class="danger" type="button" disabled>Reset Data</button>
    </div>
  </div>

  <div class="tournamentSetupGrid">
    <div class="tournamentField">
      <label for="tournamentType">Tournament Type</label>
      <select id="tournamentType" disabled>
        <option value="">-- Select --</option>
        <option value="League">&#127942; League</option>
        <option value="Groups">&#9638; Groups</option>
        <option value="Knockout">&#9889; Knockout</option>
      </select>
    </div>

    <div id="knockoutConfig" class="tournamentField tournamentField-nested hidden">
      <label for="knockoutRoundOf">Knockout Round Size</label>
      <input id="knockoutRoundOf" type="number" min="2" placeholder="e.g., 8" disabled />
      <div class="hint">If the number is odd, a BOT entry is added automatically to make the knockout round even.</div>
    </div>

    <div id="groupsConfig" class="tournamentNestedFields hidden">
      <div class="tournamentField tournamentField-compact">
        <label for="groupsCount">Number of Groups</label>
        <input id="groupsCount" type="number" min="1" max="16" step="1" inputmode="numeric" placeholder="2" disabled />
      </div>
      <div class="tournamentField tournamentField-compact">
        <label for="teamsPerGroup">Teams per Group</label>
        <input id="teamsPerGroup" type="number" min="2" max="32" step="1" inputmode="numeric" placeholder="4" disabled />
      </div>
    </div>

    <div class="tournamentField tournamentField-wide">
      <label for="fixtureType">Fixture Options</label>
      <select id="fixtureType" disabled>
        <option value="">-- Select --</option>
        <option value="Single">&#8594; Single Matches (each pair plays once)</option>
        <option value="HomeAway">&#8644; Home &amp; Away Matches (each pair plays twice)</option>
      </select>
    </div>

    <div class="tournamentField">
      <label for="playoffFormat">Playoff Format</label>
      <select id="playoffFormat" disabled>
        <option value="Semifinals">&#9670; Semifinals</option>
        <option value="Qualifiers">&#9873; Qualifiers</option>
        <option value="Final">&#127942; Final (top 2 direct)</option>
      </select>
    </div>

    <div class="tournamentTeamFields" id="tournamentTeamsSetupPanel" tabindex="-1">
      <div class="tournamentField">
        <label for="matchType">Match Type</label>
        <select id="matchType" disabled>
          <option value="">-- Select --</option>
          <option value="Singles">&#128100; Singles</option>
          <option value="Doubles">&#128101; Double</option>
        </select>
      </div>
      <div class="tournamentField tournamentField-compact">
        <label for="teamsCount">Number of Teams</label>
        <input id="teamsCount" type="number" min="2" max="64" step="1" inputmode="numeric" placeholder="4" disabled />
      </div>
    </div>
  </div>

  <div class="dataSurface tournamentTeamsPreview" id="teamsPreview"></div>
  <div class="row workspaceActionBar" style="justify-content: flex-end;">
    <button id="buildTeamsBtn" class="secondary" disabled>Build Teams</button>
    <button id="generateScheduleBtn" disabled>Generate Schedule</button>
  </div>
  </div>

  <div class="workspacePanel" id="databaseBackupPanel" style="margin-top:14px;">
    <div class="playerStatsSectionHeader">
      <h4>Database Backup &amp; Restore</h4>
      <span class="pill">Local Storage</span>
    </div>
    <div class="hint">Export tournaments, player lists, shuttle records, history settings, and leaderboard selections to a JSON backup. Login sessions and passwords are not included.</div>
    <div class="row">
      <div class="col">
        <label>Export Database</label>
        <button id="exportDatabaseBtn" type="button" class="secondary">Export DB File</button>
      </div>
      <div class="col">
        <label for="importDatabaseFile">Import Database File</label>
        <input id="importDatabaseFile" type="file" accept="application/json,.json" />
      </div>
      <div class="col">
        <label for="importDatabaseMode">Import Mode</label>
        <select id="importDatabaseMode">
          <option value="merge">Merge with Existing Data</option>
          <option value="replace">Replace Existing App Data</option>
        </select>
      </div>
    </div>
    <div class="row workspaceActionBar" style="justify-content:flex-end;">
      <button id="importDatabaseBtn" type="button" disabled>Import DB File</button>
    </div>
    <div id="databaseBackupStatus" class="authStatus"></div>
  </div>
</div>

<!-- Players tab -->
<div class="section featureView hidden" id="viewPlayers" role="tabpanel" aria-labelledby="tabPlayers">
  <div class="workspaceHeader">
    <div class="workspaceHeaderIcon" aria-hidden="true">&#128101;</div>
    <div><h3>Players</h3><div class="hint">Manage reusable player lists and apply them to the active tournament.</div></div>
  </div>

  <div class="playersManager">
    <div class="playersControlPanel">
      <div class="workspacePanel">
        <div class="playerStatsSectionHeader">
          <h4>Player Lists</h4>
          <span class="pill">Account</span>
        </div>
        <label for="playerListSelect">Saved Player List</label>
        <select id="playerListSelect">
          <option value="">-- Select --</option>
        </select>
        <div class="row workspaceActionBar" style="justify-content:flex-end;">
          <button id="loadPlayerListBtn" disabled class="secondary">Load List</button>
          <button id="savePlayerListBtn" disabled>Save Current List</button>
        </div>
      </div>

      <div class="workspacePanel">
        <div class="playerStatsSectionHeader">
          <h4>Create / Edit</h4>
          <div class="playersHeaderActions">
            <button id="toggleBulkPlayersBtn" type="button" class="secondary playerIconBtn toggleBulkPlayersBtn" aria-label="Add many players" title="Add many players" aria-expanded="false" aria-controls="bulkPlayersPanel">
              <span aria-hidden="true">&#128101;+</span>
              <span class="srOnly">Add many players</span>
            </button>
            <span class="pill">Roster</span>
          </div>
        </div>
        <label for="newPlayerName">Player Display Name</label>
        <div class="playerAddRow">
          <div class="col">
            <input id="newPlayerName" type="text" placeholder="Player name" />
          </div>
          <button id="addPlayerBtn" type="button" class="addPlayerIconBtn" disabled aria-label="Add new player" title="Add new player">
            <span aria-hidden="true">+</span>
            <span class="srOnly">Add new player</span>
          </button>
        </div>

        <div id="bulkPlayersPanel" class="bulkPlayersPanel" hidden>
          <label for="bulkPlayersInput">Add Many Players</label>
          <textarea id="bulkPlayersInput" rows="4" placeholder="One player per line or comma-separated"></textarea>
          <div class="row workspaceActionBar" style="justify-content:flex-end;">
            <button id="bulkAddPlayersBtn" class="secondary" disabled>
              <span aria-hidden="true">&#128101;+</span>
              <span>Add Many</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="workspacePanel playersRosterPanel">
      <div class="playersRosterHeader">
        <h4>Player Roster</h4>
        <span class="pill" id="playersRosterCount">0 Players</span>
      </div>
      <div class="playersRosterGrid" id="playersList"></div>
    </div>
  </div>
</div>

<!-- Shuttle Management tab -->
<div class="section featureView hidden" id="viewShuttles" role="tabpanel" aria-labelledby="tabShuttles">
  <div class="workspaceHeader">
    <div class="workspaceHeaderIcon" aria-hidden="true">&#128230;</div>
    <div><h3>Shuttle Management</h3><div class="hint">Track stock, usage, and borrowed shuttles. Each new box adds 6 pieces.</div></div>
  </div>

  <div class="shuttleSummary">
    <div class="statCard">
      <div class="statValue" id="shuttleAvailableCount">0</div>
      <div class="statLabel">Available</div>
    </div>
    <div class="statCard">
      <div class="statValue" id="shuttlePurchasedCount">0</div>
      <div class="statLabel">Purchased</div>
    </div>
    <div class="statCard">
      <div class="statValue" id="shuttleUsedCount">0</div>
      <div class="statLabel">Used for Play</div>
    </div>
    <div class="statCard">
      <div class="statValue" id="shuttleBorrowedCount">0</div>
      <div class="statLabel">With Borrowers</div>
    </div>
  </div>

  <div class="shuttlePanel">
    <h4>Add New Stock</h4>
    <div class="hint">Enter the number of boxes purchased. Six shuttles are added for every box.</div>
    <div class="row">
      <div class="col">
        <label for="shuttlePurchaseDate">Purchase Date</label>
        <input id="shuttlePurchaseDate" type="date" />
      </div>
      <div class="col">
        <label for="shuttleBoxesBought">Number of Boxes</label>
        <input id="shuttleBoxesBought" type="number" min="1" step="1" placeholder="e.g., 2" />
      </div>
      <div class="col">
        <label for="shuttlePurchaseNote">Note (Optional)</label>
        <input id="shuttlePurchaseNote" type="text" placeholder="Brand or shop" />
      </div>
    </div>
    <button id="addShuttleStockBtn" disabled>Add to Stock</button>
  </div>

  <div class="shuttlePanel">
    <h4>Record Shuttle Taken</h4>
    <div class="hint">Record who took shuttles and whether they were used for play or borrowed to return later.</div>
    <div class="row">
      <div class="col">
        <label for="shuttleTakenDate">Date</label>
        <input id="shuttleTakenDate" type="date" />
      </div>
      <div class="col">
        <label for="shuttleTakenTime">Time</label>
        <input id="shuttleTakenTime" type="time" />
      </div>
      <div class="col">
        <label for="shuttleTakenBy">Person Name</label>
        <input id="shuttleTakenBy" type="text" placeholder="Who took the shuttle?" />
      </div>
      <div class="col">
        <label for="shuttleTakenQuantity">Number of Shuttles</label>
        <input id="shuttleTakenQuantity" type="number" min="1" step="1" placeholder="e.g., 1" />
      </div>
      <div class="col">
        <label for="shuttleTakenType">Reason</label>
        <select id="shuttleTakenType">
          <option value="used">Used for Play</option>
          <option value="borrowed">Borrowed (Return Expected)</option>
        </select>
      </div>
    </div>
    <button id="recordShuttleTakenBtn" disabled>Record</button>
    <div id="shuttleFormStatus" class="authStatus"></div>
  </div>

  <div class="shuttlePanel">
    <h4>Borrowed Shuttles to Follow Up</h4>
    <div id="shuttleBorrowersOutput"></div>
  </div>

  <div class="shuttlePanel">
    <h4>Stock and Usage History</h4>
    <div id="shuttleHistoryOutput"></div>
  </div>
</div>

<!-- Groups tab -->
<div class="section featureView hidden" id="viewGroups" role="tabpanel" aria-labelledby="tabGroups">
  <div class="workspaceHeader">
    <div class="workspaceHeaderIcon" aria-hidden="true">&#129513;</div>
    <div><h3>Groups</h3><div class="hint">Review team placement and complete every group before generating fixtures.</div></div>
  </div>
  <div class="dataSurface" id="groupsAssignmentOutput"></div>
  <div class="row workspaceActionBar" style="justify-content: flex-end;">
    <button id="autoFillGroupsBtn" class="secondary" disabled>Auto Fill Groups</button>
    <button id="groupsGenerateScheduleBtn" disabled>Generate Schedule</button>
  </div>
</div>

<!-- Schedule tab -->
<div class="section featureView hidden" id="viewSchedule" role="tabpanel" aria-labelledby="tabSchedule">
  <div class="workspaceHeader">
    <div class="workspaceHeaderIcon" aria-hidden="true">&#128467;</div>
    <div><h3>Schedule</h3><div class="hint">Enter every score. Eligible Finals fixtures appear automatically above the regular schedule.</div></div>
  </div>
  <div id="scheduleFinalsArea" class="hidden">
    <div class="playerStatsSection" style="margin:12px 0 18px;">
      <div class="playerStatsSectionHeader">
        <h4>Finals Schedule</h4>
        <span class="pill" id="scheduleFinalsFormat">Playoffs</span>
      </div>
      <div id="scheduleFinalsStatus" class="hint"></div>
      <div id="finalMatchOutput"></div>
      <div id="playoffBracketOutput"></div>
    </div>
  </div>
  <h4 id="regularScheduleHeading" style="margin:12px 0 6px">Scheduled Games</h4>
  <div id="scheduleOutput"></div>
</div>

<!-- Points Table tab -->
<div class="section featureView hidden" id="viewPoints" role="tabpanel" aria-labelledby="tabPoints">
  <div class="workspaceHeader">
    <div class="workspaceHeaderIcon" aria-hidden="true">&#128202;</div>
    <div><h3>Points Table</h3><div class="hint">Live standings. Select a team to view its fixtures; score changes save automatically and update Schedule.</div></div>
  </div>
  <div class="dataSurface" id="pointsTableOutput"></div>
</div>

<div class="section featureView adminOnly hidden" id="usersAdminSection" role="tabpanel" aria-labelledby="tabUsers">
  <div class="row" style="justify-content: space-between; align-items: flex-start;">
    <div class="workspaceHeader" style="flex:1; margin:0;">
      <div class="workspaceHeaderIcon" aria-hidden="true">&#9881;</div>
      <div><h3>Users</h3><div class="hint">Create accounts, manage access, and reset temporary passwords.</div></div>
    </div>
    <button id="logoutBtn" class="secondary">Logout</button>
  </div>

  <div class="row workspacePanel" style="margin-top:18px;">
    <div class="col">
      <label for="newUserUsername">Username</label>
      <input id="newUserUsername" type="text" placeholder="e.g., scorer1" />
    </div>
    <div class="col">
      <label for="newUserDisplayName">Display Name</label>
      <input id="newUserDisplayName" type="text" placeholder="e.g., Court Scorer" />
    </div>
  </div>
  <div class="row workspacePanel">
    <div class="col">
      <label for="newUserPassword">Temporary Password</label>
      <input id="newUserPassword" type="text" placeholder="At least 8 characters" />
    </div>
    <div class="col">
      <label for="newUserIsAdmin">Admin</label>
      <select id="newUserIsAdmin">
        <option value="false">No</option>
        <option value="true">Yes</option>
      </select>
    </div>
  </div>
  <button id="createUserBtn">Create User</button>
  <div id="createUserStatus" class="authStatus"></div>
  <div id="usersList" style="margin-top:14px"></div>
</div>

<!-- Leaderboard tab -->
<div class="section featureView hidden" id="viewLeaderboard" role="tabpanel" aria-labelledby="tabLeaderboard">
  <div class="workspaceHeader">
    <div class="workspaceHeaderIcon" aria-hidden="true">&#127942;</div>
    <div><h3>Monthly Leaderboard</h3>
  <div class="hint">Player-based global leaderboard on this browser. Sorted by: Total Points → Titles → Runner-up → Games Played → Days Played.</div>
    </div>
  </div>
  <div class="hint">Original leaderboard scoring, calculated only from tournaments in the selected month.</div>
  <div class="row workspacePanel" id="leaderboardPeriodControls">
    <div class="col">
      <label for="leaderboardYearSelect">Year</label>
      <select id="leaderboardYearSelect"></select>
    </div>
    <div class="col">
      <label for="leaderboardMonthSelect">Month / Range</label>
      <select id="leaderboardMonthSelect">
        <option value="all">All Time</option>
        <option value="01">January</option><option value="02">February</option><option value="03">March</option>
        <option value="04">April</option><option value="05">May</option><option value="06">June</option>
        <option value="07">July</option><option value="08">August</option><option value="09">September</option>
        <option value="10">October</option><option value="11">November</option><option value="12">December</option>
      </select>
    </div>
  </div>
  <div id="leaderboardPeriodSummary" class="statSubtle"></div>
  <div class="dataSurface" id="finalLeaderboardOutput"></div>
</div>

<!-- Player Statistics tab -->
<div class="section featureView hidden" id="viewStats" role="tabpanel" aria-labelledby="tabStats">
  <div class="workspaceHeader">
    <div class="workspaceHeaderIcon" aria-hidden="true">&#128200;</div>
    <div><h3>Player Statistics</h3><div class="hint">Explore form, results, titles, partners, and opponents for any player.</div></div>
  </div>
  <div class="row workspacePanel">
    <div class="col">
      <label for="playerStatsSelect">Player</label>
      <select id="playerStatsSelect">
        <option value="">-- Select Player --</option>
      </select>
    </div>
    <div class="col">
      <label for="playerStatsYearSelect">Year</label>
      <select id="playerStatsYearSelect"></select>
    </div>
    <div class="col">
      <label for="playerStatsMonthSelect">Month / Range</label>
      <select id="playerStatsMonthSelect">
        <option value="all">All Time</option>
        <option value="01">January</option><option value="02">February</option><option value="03">March</option>
        <option value="04">April</option><option value="05">May</option><option value="06">June</option>
        <option value="07">July</option><option value="08">August</option><option value="09">September</option>
        <option value="10">October</option><option value="11">November</option><option value="12">December</option>
      </select>
    </div>
  </div>
  <div id="playerStatsPeriodSummary" class="statSubtle"></div>
  <div id="playerStatsOutput" style="margin-top:12px"></div>
</div>

<!-- History tab -->
<div class="section featureView hidden" id="viewHistory" role="tabpanel" aria-labelledby="tabHistory">
  <div class="workspaceHeader">
    <div class="workspaceHeaderIcon" aria-hidden="true">&#128344;</div>
    <div><h3>History</h3><div class="hint">Review saved tournament details without changing the active event.</div></div>
  </div>
  <div class="historyCalendar">
    <div class="historyCalendarHeader">
      <button id="historyPreviousMonthBtn" type="button" aria-label="Previous month">&#8249;</button>
      <div id="historyCalendarMonth" class="historyCalendarMonth"></div>
      <button id="historyNextMonthBtn" type="button" aria-label="Next month">&#8250;</button>
    </div>
    <div class="historyCalendarWeekdays" aria-hidden="true">
      <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
    </div>
    <div id="historyCalendarGrid" class="historyCalendarGrid" role="grid" aria-label="Tournament history calendar"></div>
    <div id="historyCalendarStatus" class="historyCalendarStatus"></div>
  </div>
  <div class="row workspacePanel">
    <div class="col">
      <label for="historyTournamentSelect">Tournaments on Selected Date</label>
      <select id="historyTournamentSelect">
        <option value="">-- Select Tournament --</option>
      </select>
    </div>
  </div>
  <div class="workspacePanel" id="historySchedulePanel">
    <div class="playerStatsSectionHeader">
      <h4>Schedule a Tournament</h4>
      <span class="pill" id="historyScheduleDateLabel">Select a date</span>
    </div>
    <div class="hint">Select a calendar tile, enter a tournament name, and add it to that date. It will also appear in the Tournament tab for loading later.</div>
    <div class="row">
      <div class="col">
        <label for="historyScheduledTournamentName">Tournament Name</label>
        <input id="historyScheduledTournamentName" type="text" placeholder="e.g., Sunday Smash" />
      </div>
      <div style="padding-top:30px">
        <button id="historyScheduleTournamentBtn" type="button" disabled>Schedule Tournament</button>
      </div>
    </div>
    <div id="historyScheduleStatus" class="authStatus"></div>
  </div>
  <div id="historyOutput" class="dataSurface"></div>
</div>

<script>
  // ----------------------------
  // Storage keys
  // ----------------------------
const STORAGE_KEYS = {
  tournamentsIndex: 'bt_tournaments_index_v1',
  tournamentPrefix: 'bt_tournament_v1_',
  playerListsIndex: 'bt_playerlists_index_v1',
  playerListPrefix: 'bt_playerlist_v1_',
  globalLeaderboard: 'bt_global_leaderboard_v1',
  leaderboardPeriod: 'bt_leaderboard_period_v1',
  leaderboardCurrentMonth: 'bt_leaderboard_current_month_v1',
  homePlayersDraft: 'bt_home_players_draft_v1',
  activeTournamentId: 'bt_active_tournament_id_v1',
  activeView: 'bt_active_view_v1',
  historyTournamentId: 'bt_history_tournament_id_v1',
  historyDate: 'bt_history_date_v1',
  playerStatsPlayer: 'bt_player_stats_player_v1',
  playerStatsPeriod: 'bt_player_stats_period_v1',
  shuttleManagement: 'bt_shuttle_management_v1',
  homePlayerPhotos: 'bt_home_player_photos_v1',
};

  // ----------------------------
  // DOM
  // ----------------------------
  // Views (one feature per tab)
  const viewTournament = document.getElementById('viewTournament');
  const viewPlayers = document.getElementById('viewPlayers');
  const viewShuttles = document.getElementById('viewShuttles');
  const viewGroups = document.getElementById('viewGroups');
  const viewSchedule = document.getElementById('viewSchedule');
  const viewPoints = document.getElementById('viewPoints');
  const viewLeaderboard = document.getElementById('viewLeaderboard');
  const viewStats = document.getElementById('viewStats');
  const viewHistory = document.getElementById('viewHistory');
  const viewUsers = document.getElementById('usersAdminSection');
  const bottomNav = document.getElementById('bottomNav');

  const newTournamentNameInput = document.getElementById('newTournamentName');
  const tournamentActionChooser = document.getElementById('tournamentActionChooser');
  const startCreateTournamentBtn = document.getElementById('startCreateTournamentBtn');
  const startLoadTournamentBtn = document.getElementById('startLoadTournamentBtn');
  const createTournamentPanel = document.getElementById('createTournamentPanel');
  const createTournamentBtn = document.getElementById('createTournamentBtn');
  const tournamentCreateBackBtn = document.getElementById('tournamentCreateBackBtn');
  const loadTournamentPanel = document.getElementById('loadTournamentPanel');
  const loadTournamentSelect = document.getElementById('loadTournamentSelect');
  const loadTournamentBtn = document.getElementById('loadTournamentBtn');
  const deleteTournamentBtn = document.getElementById('deleteTournamentBtn');
  const tournamentLoadBackBtn = document.getElementById('tournamentLoadBackBtn');
  const databaseBackupPanel = document.getElementById('databaseBackupPanel');
  const exportDatabaseBtn = document.getElementById('exportDatabaseBtn');
  const importDatabaseFile = document.getElementById('importDatabaseFile');
  const importDatabaseMode = document.getElementById('importDatabaseMode');
  const importDatabaseBtn = document.getElementById('importDatabaseBtn');
  const databaseBackupStatus = document.getElementById('databaseBackupStatus');
  const tournamentTeamsSetupPanel = document.getElementById('tournamentTeamsSetupPanel');

  const bottomNavButtons = () => document.querySelectorAll('.bottomNavBtn');

  const currentTournamentNameEl = document.getElementById('currentTournamentName');
  const tournamentSetupPanel = document.getElementById('tournamentSetupPanel');
  const closeTournamentBtn = document.getElementById('closeTournamentBtn');
  const resetTournamentBtn = document.getElementById('resetTournamentBtn');

  const tournamentTypeSelect = document.getElementById('tournamentType');
  const groupsConfig = document.getElementById('groupsConfig');
  const knockoutConfig = document.getElementById('knockoutConfig');
  const knockoutRoundOfInput = document.getElementById('knockoutRoundOf');
  const groupsCountInput = document.getElementById('groupsCount');
  const teamsPerGroupInput = document.getElementById('teamsPerGroup');
  const fixtureTypeSelect = document.getElementById('fixtureType');
  const playoffFormatSelect = document.getElementById('playoffFormat');

  // Players + teams generation
  const matchTypeSelect = document.getElementById('matchType');
  const teamsCountInput = document.getElementById('teamsCount');
  const playerListSelect = document.getElementById('playerListSelect');
  const loadPlayerListBtn = document.getElementById('loadPlayerListBtn');
  const savePlayerListBtn = document.getElementById('savePlayerListBtn');
  const newPlayerNameInput = document.getElementById('newPlayerName');
  const addPlayerBtn = document.getElementById('addPlayerBtn');
  const toggleBulkPlayersBtn = document.getElementById('toggleBulkPlayersBtn');
  const bulkPlayersPanel = document.getElementById('bulkPlayersPanel');
  const bulkPlayersInput = document.getElementById('bulkPlayersInput');
  const bulkAddPlayersBtn = document.getElementById('bulkAddPlayersBtn');
  const playersListDiv = document.getElementById('playersList');
  const playersRosterCount = document.getElementById('playersRosterCount');
  const buildTeamsBtn = document.getElementById('buildTeamsBtn');
  const teamsPreviewDiv = document.getElementById('teamsPreview');
  const groupsAssignmentOutput = document.getElementById('groupsAssignmentOutput');
  const autoFillGroupsBtn = document.getElementById('autoFillGroupsBtn');
  const groupsGenerateScheduleBtn = document.getElementById('groupsGenerateScheduleBtn');

  const generateScheduleBtn = document.getElementById('generateScheduleBtn');

  const scheduleOutput = document.getElementById('scheduleOutput');
  const playoffBracketOutput = document.getElementById('playoffBracketOutput');
  const finalMatchOutput = document.getElementById('finalMatchOutput');
  const scheduleFinalsArea = document.getElementById('scheduleFinalsArea');
  const scheduleFinalsStatus = document.getElementById('scheduleFinalsStatus');
  const scheduleFinalsFormat = document.getElementById('scheduleFinalsFormat');
  const pointsTableOutput = document.getElementById('pointsTableOutput');
  const finalLeaderboardOutput = document.getElementById('finalLeaderboardOutput');
  const leaderboardYearSelect = document.getElementById('leaderboardYearSelect');
  const leaderboardMonthSelect = document.getElementById('leaderboardMonthSelect');
  const leaderboardPeriodSummary = document.getElementById('leaderboardPeriodSummary');
  const playerStatsSelect = document.getElementById('playerStatsSelect');
  const playerStatsOutput = document.getElementById('playerStatsOutput');
  const playerStatsYearSelect = document.getElementById('playerStatsYearSelect');
  const playerStatsMonthSelect = document.getElementById('playerStatsMonthSelect');
  const playerStatsPeriodSummary = document.getElementById('playerStatsPeriodSummary');
  const historyTournamentSelect = document.getElementById('historyTournamentSelect');
  const historyOutput = document.getElementById('historyOutput');
  const historyCalendarGrid = document.getElementById('historyCalendarGrid');
  const historyCalendarMonth = document.getElementById('historyCalendarMonth');
  const historyCalendarStatus = document.getElementById('historyCalendarStatus');
  const historyPreviousMonthBtn = document.getElementById('historyPreviousMonthBtn');
  const historyNextMonthBtn = document.getElementById('historyNextMonthBtn');
  const historyScheduleDateLabel = document.getElementById('historyScheduleDateLabel');
  const historyScheduledTournamentName = document.getElementById('historyScheduledTournamentName');
  const historyScheduleTournamentBtn = document.getElementById('historyScheduleTournamentBtn');
  const historyScheduleStatus = document.getElementById('historyScheduleStatus');

  const shuttleAvailableCount = document.getElementById('shuttleAvailableCount');
  const shuttlePurchasedCount = document.getElementById('shuttlePurchasedCount');
  const shuttleUsedCount = document.getElementById('shuttleUsedCount');
  const shuttleBorrowedCount = document.getElementById('shuttleBorrowedCount');
  const shuttlePurchaseDate = document.getElementById('shuttlePurchaseDate');
  const shuttleBoxesBought = document.getElementById('shuttleBoxesBought');
  const shuttlePurchaseNote = document.getElementById('shuttlePurchaseNote');
  const addShuttleStockBtn = document.getElementById('addShuttleStockBtn');
  const shuttleTakenDate = document.getElementById('shuttleTakenDate');
  const shuttleTakenTime = document.getElementById('shuttleTakenTime');
  const shuttleTakenBy = document.getElementById('shuttleTakenBy');
  const shuttleTakenQuantity = document.getElementById('shuttleTakenQuantity');
  const shuttleTakenType = document.getElementById('shuttleTakenType');
  const recordShuttleTakenBtn = document.getElementById('recordShuttleTakenBtn');
  const shuttleFormStatus = document.getElementById('shuttleFormStatus');
  const shuttleBorrowersOutput = document.getElementById('shuttleBorrowersOutput');
  const shuttleHistoryOutput = document.getElementById('shuttleHistoryOutput');

  document.querySelectorAll('.authStatus').forEach(el => {
    el.setAttribute('role', 'status');
    el.setAttribute('aria-live', 'polite');
    el.setAttribute('aria-atomic', 'true');
  });

  // active view
  let currentView = 'tournament';
  let historyCalendarCursor = null;
  let leaderboardControlsInitialized = false;
  let scoreRenderTimer = null;

  // ----------------------------
  // State
  // ----------------------------
  /** @type {{
    id:string,
    name:string,
    type:'League'|'Groups'|'Knockout'|'',
    fixtureType:'Single'|'HomeAway'|'',
    matchType:'Singles'|'Doubles'|'',
    teamsCount:number,
    groupsCount:number,
    teamsPerGroup:number,
    players:string[],
    teams:string[],
    teamPlayers:Record<string,string[]>,
    matches:any[],
    knockout:any,
    finalResult:any|null
  } | null} */
  let tournament = null;
  let tournamentEntryMode = null; // 'create' | 'load' | null
  let expandedPointsTeam = null;

  function setTournamentFlowElementHidden(el, hidden) {
    if (!el) return;
    el.classList.toggle('hidden', hidden);
    el.hidden = hidden;
  }

  function updateTournamentHomePanels() {
    const hasTournament = !!tournament;
    setTournamentFlowElementHidden(tournamentActionChooser, hasTournament || !!tournamentEntryMode);
    setTournamentFlowElementHidden(createTournamentPanel, hasTournament || tournamentEntryMode !== 'create');
    setTournamentFlowElementHidden(loadTournamentPanel, hasTournament || tournamentEntryMode !== 'load');
  }

  function hideAllViews() {
    [
      viewTournament,
      viewPlayers,
      viewShuttles,
      viewGroups,
      viewSchedule,
      viewPoints,
      viewLeaderboard,
      viewStats,
      viewHistory,
      viewUsers,
    ].forEach(v => {
      if (!v) return;
      v.classList.add('hidden');
      v.hidden = true;
      v.setAttribute('aria-hidden', 'true');
    });
  }

  function updateBottomNavState() {
    const hasTournament = !!tournament;
    const hasSchedule = hasTournament && Array.isArray(tournament?.matches) && tournament.matches.length > 0;

    if (tournamentSetupPanel) {
      tournamentSetupPanel.classList.toggle('hidden', !hasTournament);
      tournamentSetupPanel.hidden = !hasTournament;
    }
    updateTournamentHomePanels();

    // Enable/disable form controls based on tournament availability
    tournamentTypeSelect.disabled = !hasTournament;
    knockoutRoundOfInput.disabled = !hasTournament || tournamentTypeSelect.value !== 'Knockout';
    fixtureTypeSelect.disabled = !hasTournament || tournamentTypeSelect.value === 'Knockout';
    playoffFormatSelect.disabled = !hasTournament || tournamentTypeSelect.value === 'Knockout';
    matchTypeSelect.disabled = !hasTournament;
    teamsCountInput.disabled = !hasTournament || tournamentTypeSelect.value === 'Knockout';
    resetTournamentBtn.disabled = !hasTournament;

    // groups inputs depend on tournament + type
    const groupsEnabled = hasTournament && tournamentTypeSelect.value === 'Groups';
    const knockoutEnabled = hasTournament && tournamentTypeSelect.value === 'Knockout';
    const builtTeamsCount = (tournament?.teams || []).length;
    const requestedTeamsCount = getRequestedTeamsCount();
    const teamsBuiltForCount = builtTeamsCount >= 2 && builtTeamsCount === requestedTeamsCount;
    groupsCountInput.disabled = !groupsEnabled;
    teamsPerGroupInput.disabled = !groupsEnabled;

    bottomNavButtons().forEach(btn => {
      const v = btn.dataset.view;
      if (v === 'tournament' || v === 'players' || v === 'shuttles' || v === 'leaderboard' || v === 'stats' || v === 'history') {
        btn.disabled = false;
      } else if (v === 'users') {
        const isAdmin = !!currentAuthSession()?.isAdmin;
        btn.disabled = !isAdmin;
        btn.hidden = !isAdmin;
      } else if (v === 'groups') {
        btn.disabled = !(groupsEnabled && teamsBuiltForCount);
      } else {
        // schedule/points/finals
        btn.disabled = !hasSchedule;
      }

      if (v === currentView) btn.classList.add('active');
      else btn.classList.remove('active');
      btn.setAttribute('aria-selected', v === currentView ? 'true' : 'false');
      btn.setAttribute('tabindex', v === currentView ? '0' : '-1');
    });
  }

  function setView(view) {
    if (view === 'final' || view === 'playoff') view = 'schedule';
    if (view === 'teams') view = 'tournament';
    if (view === 'users' && !currentAuthSession()?.isAdmin) view = 'tournament';
    // Guard against disabled navigation
    const btn = document.querySelector(`.bottomNavBtn[data-view="${view}"]`);
    if (btn && btn.disabled) return;

    currentView = view;
    localStorage.setItem(STORAGE_KEYS.activeView, view);
    hideAllViews();
    const map = {
      tournament: viewTournament,
      players: viewPlayers,
      shuttles: viewShuttles,
      groups: viewGroups,
      schedule: viewSchedule,
      points: viewPoints,
      leaderboard: viewLeaderboard,
      stats: viewStats,
      history: viewHistory,
      users: viewUsers,
    };
    const el = map[view] || viewTournament;
    if (el) {
      el.classList.remove('hidden');
      el.hidden = false;
      el.setAttribute('aria-hidden', 'false');
    }

    // Keep views fresh
    if (view === 'players') {
      refreshHomeDropdowns();
      renderPlayersList();
      updateSavePlayerListBtnState();
    }
    if (view === 'shuttles') {
      renderShuttleManagement();
    }
    if (view === 'leaderboard') {
      refreshLeaderboardPeriodControls();
      renderGlobalLeaderboard(computeGlobalLeaderboardRows());
    }
    if (view === 'stats') {
      refreshPlayerStatsPeriodControls();
      refreshPlayerStatsSelect();
      renderSelectedPlayerStats();
    }
    if (view === 'history') {
      refreshHistoryDropdown();
      renderSelectedHistoryTournament();
    }
    if (view === 'teams') {
      renderTeamsPreview();
      updateBuildTeamsBtn();
    }
    if (view === 'groups') {
      renderGroupsAssignment();
      updateGenerateScheduleBtn();
    }
    if (view === 'schedule') {
      renderSchedule(tournament?.matches || []);
      recalcAndRender();
    }
    if (view === 'points') {
      recalcAndRender();
    }
    if (view === 'users') {
      renderUsersAdmin();
    }

    updateBottomNavState();
    // Do not auto-scroll here. Keeping scroll/focus stable prevents input entry
    // from feeling like it jumped to another section on mobile browsers.
  }

  function showTournamentTeamsSetup() {
    setView('tournament');
    renderTeamsPreview();
    updateBuildTeamsBtn();
    window.setTimeout(() => {
      if (!tournamentTeamsSetupPanel) return;
      try {
        tournamentTeamsSetupPanel.focus({ preventScroll: true });
      } catch {
        tournamentTeamsSetupPanel.focus();
      }
      tournamentTeamsSetupPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 0);
  }

  // ----------------------------
  // Utilities
  // ----------------------------
  function uid() {
    return Math.random().toString(36).slice(2) + Date.now().toString(36);
  }

  function normalizeTeamName(name) {
    return (name || '').trim();
  }

  function normalizePlayerName(name) {
    return (name || '').trim();
  }

  function splitPlayerNames(text) {
    // Supports newline-separated or comma-separated input.
    return (text || '')
      .split(/[\n,]+/g)
      .map(s => normalizePlayerName(s))
      .filter(Boolean);
  }

  function playerInitials(name) {
    const parts = normalizePlayerName(name).split(/\s+/).filter(Boolean);
    if (!parts.length) return '?';
    return parts.slice(0, 2).map(part => part[0]).join('').toUpperCase();
  }

  function readPlayerPhotoFile(file) {
    return new Promise((resolve, reject) => {
      if (!file || !file.type.startsWith('image/')) {
        reject(new Error('Select an image file.'));
        return;
      }
      const reader = new FileReader();
      reader.onerror = () => reject(new Error('Could not read the image file.'));
      reader.onload = () => {
        const image = new Image();
        image.onerror = () => reject(new Error('Could not load the selected image.'));
        image.onload = () => {
          const size = 192;
          const canvas = document.createElement('canvas');
          canvas.width = size;
          canvas.height = size;
          const ctx = canvas.getContext('2d');
          ctx.fillStyle = '#ffffff';
          ctx.fillRect(0, 0, size, size);
          const scale = Math.max(size / image.width, size / image.height);
          const width = image.width * scale;
          const height = image.height * scale;
          ctx.drawImage(image, (size - width) / 2, (size - height) / 2, width, height);
          resolve(canvas.toDataURL('image/jpeg', 0.78));
        };
        image.src = String(reader.result || '');
      };
      reader.readAsDataURL(file);
    });
  }

  function lower(name) {
    return name.trim().toLowerCase();
  }

  function validatePlayersUniquenessForList(list) {
    const set = new Set();
    for (const p of (list || [])) {
      const n = lower(normalizePlayerName(p));
      if (!n) return false;
      if (set.has(n)) return false;
      set.add(n);
    }
    return true;
  }

  function safeJsonParse(str, fallback) {
    try { return JSON.parse(str); } catch { return fallback; }
  }

  // ----------------------------
  // Local/file database backup
  // ----------------------------
  function isLocalFileStorageMode() {
    return document.documentElement.dataset.storageMode === 'local';
  }

  function getLocalAppStorageItems() {
    const items = {};
    for (let index = 0; index < localStorage.length; index += 1) {
      const key = localStorage.key(index);
      if (key && key.startsWith('bt_')) items[key] = localStorage.getItem(key);
    }
    return items;
  }

  function exportLocalDatabase() {
    if (!isLocalFileStorageMode()) return;
    const items = getLocalAppStorageItems();
    const backup = {
      format: 'badminton-tournament-manager-local-backup',
      version: 1,
      exportedAt: new Date().toISOString(),
      recordCount: Object.keys(items).length,
      items,
    };
    const blob = new Blob([JSON.stringify(backup, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    const stamp = todayForDateInput();
    link.href = url;
    link.download = `badminton-tournament-backup-${stamp}.json`;
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.setTimeout(() => URL.revokeObjectURL(url), 1000);
    setAuthStatus(databaseBackupStatus, `Exported ${backup.recordCount} local database records.`, 'success');
  }

  function validateLocalDatabaseBackup(parsed) {
    if (!parsed || parsed.format !== 'badminton-tournament-manager-local-backup' || parsed.version !== 1) {
      throw new Error('This is not a supported Badminton Tournament Manager backup file.');
    }
    if (!parsed.items || typeof parsed.items !== 'object' || Array.isArray(parsed.items)) {
      throw new Error('The backup file does not contain a valid items collection.');
    }
    const entries = Object.entries(parsed.items);
    if (!entries.length) throw new Error('The backup file contains no app data.');
    entries.forEach(([key, value]) => {
      if (!key.startsWith('bt_') || typeof value !== 'string') {
        throw new Error('The backup contains an invalid local-storage record.');
      }
    });
    return entries;
  }

  async function importLocalDatabase(file, mode) {
    if (!isLocalFileStorageMode() || !file) return;
    const parsed = JSON.parse(await file.text());
    const entries = validateLocalDatabaseBackup(parsed);

    if (mode === 'replace') {
      const approved = confirm('Replace all existing local app data with this backup? This cannot be undone unless you export the current data first.');
      if (!approved) return false;
      const existingKeys = Object.keys(getLocalAppStorageItems());
      existingKeys.forEach(key => localStorage.removeItem(key));
    }

    entries.forEach(([key, value]) => localStorage.setItem(key, value));
    setAuthStatus(databaseBackupStatus, `Imported ${entries.length} records. Reloading the app...`, 'success');
    window.setTimeout(() => location.reload(), 700);
    return true;
  }

  // ----------------------------
  // Shuttle stock management
  // ----------------------------
  function todayForDateInput() {
    const now = new Date();
    const offset = now.getTimezoneOffset() * 60000;
    return new Date(now.getTime() - offset).toISOString().slice(0, 10);
  }

  function currentTimeForInput() {
    const now = new Date();
    return `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
  }

  function getShuttleData() {
    const saved = safeJsonParse(localStorage.getItem(STORAGE_KEYS.shuttleManagement) || '{}', {});
    return {
      purchases: Array.isArray(saved.purchases) ? saved.purchases : [],
      transactions: Array.isArray(saved.transactions) ? saved.transactions : [],
    };
  }

  function saveShuttleData(data) {
    localStorage.setItem(STORAGE_KEYS.shuttleManagement, JSON.stringify(data));
  }

  function getShuttleTotals(data = getShuttleData()) {
    const purchased = data.purchases.reduce((sum, item) => sum + (Number(item.boxes) || 0) * 6, 0);
    const used = data.transactions
      .filter(item => item.type === 'used')
      .reduce((sum, item) => sum + (Number(item.quantity) || 0), 0);
    const borrowed = data.transactions
      .filter(item => item.type === 'borrowed' && !item.returned)
      .reduce((sum, item) => sum + (Number(item.quantity) || 0), 0);
    return { purchased, used, borrowed, available: Math.max(0, purchased - used - borrowed) };
  }

  function appendShuttleCell(row, value) {
    const cell = document.createElement('td');
    if (value instanceof Node) cell.appendChild(value);
    else cell.textContent = String(value ?? '');
    row.appendChild(cell);
    return cell;
  }

  function createShuttleTable(headers) {
    const table = document.createElement('table');
    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');
    headers.forEach(label => {
      const th = document.createElement('th');
      th.scope = 'col';
      th.textContent = label;
      headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);
    table.appendChild(thead);
    const tbody = document.createElement('tbody');
    table.appendChild(tbody);
    return { table, tbody };
  }

  function renderShuttleBorrowers(data) {
    if (!shuttleBorrowersOutput) return;
    shuttleBorrowersOutput.innerHTML = '';
    const borrowed = data.transactions
      .filter(item => item.type === 'borrowed' && !item.returned)
      .sort((a, b) => String(a.date).localeCompare(String(b.date)));

    if (!borrowed.length) {
      shuttleBorrowersOutput.innerHTML = '<div class="hint" style="margin:0">No outstanding borrowed shuttles.</div>';
      return;
    }

    const { table, tbody } = createShuttleTable(['Person', 'Shuttles', 'Borrowed On', 'Time', 'Status', 'Action']);
    borrowed.forEach(item => {
      const row = document.createElement('tr');
      appendShuttleCell(row, item.person);
      appendShuttleCell(row, item.quantity);
      appendShuttleCell(row, item.date);
      appendShuttleCell(row, item.time || '-');
      const status = document.createElement('span');
      status.className = 'shuttleStatus borrowed';
      status.textContent = 'Return due';
      appendShuttleCell(row, status);
      const returnBtn = document.createElement('button');
      returnBtn.type = 'button';
      returnBtn.className = 'smallBtn secondary';
      returnBtn.textContent = 'Mark Returned';
      returnBtn.addEventListener('click', () => {
        const latest = getShuttleData();
        const record = latest.transactions.find(entry => entry.id === item.id);
        if (!record) return;
        record.returned = true;
        record.returnedDate = todayForDateInput();
        saveShuttleData(latest);
        renderShuttleManagement();
      });
      appendShuttleCell(row, returnBtn);
      tbody.appendChild(row);
    });
    shuttleBorrowersOutput.appendChild(table);
  }

  function renderShuttleHistory(data) {
    if (!shuttleHistoryOutput) return;
    shuttleHistoryOutput.innerHTML = '';
    const events = [
      ...data.purchases.map(item => ({ ...item, event: 'Stock Added', quantity: (Number(item.boxes) || 0) * 6 })),
      ...data.transactions.map(item => ({ ...item, event: item.type === 'borrowed' ? 'Borrowed' : 'Used for Play' })),
    ].sort((a, b) => String(b.date).localeCompare(String(a.date)) || Number(b.createdAt || 0) - Number(a.createdAt || 0));

    if (!events.length) {
      shuttleHistoryOutput.innerHTML = '<div class="hint" style="margin:0">No shuttle activity recorded yet.</div>';
      return;
    }

    const { table, tbody } = createShuttleTable(['Date', 'Time', 'Activity', 'Person / Note', 'Boxes', 'Shuttles', 'Status']);
    events.forEach(item => {
      const row = document.createElement('tr');
      appendShuttleCell(row, item.date);
      appendShuttleCell(row, item.time || '-');
      appendShuttleCell(row, item.event);
      appendShuttleCell(row, item.person || item.note || '-');
      appendShuttleCell(row, item.event === 'Stock Added' ? item.boxes : '-');
      appendShuttleCell(row, item.quantity);
      if (item.type === 'borrowed') {
        const status = document.createElement('span');
        status.className = `shuttleStatus ${item.returned ? 'returned' : 'borrowed'}`;
        status.textContent = item.returned ? `Returned ${item.returnedDate || ''}`.trim() : 'Return due';
        appendShuttleCell(row, status);
      } else {
        appendShuttleCell(row, item.event === 'Stock Added' ? 'In stock' : 'Consumed');
      }
      tbody.appendChild(row);
    });
    shuttleHistoryOutput.appendChild(table);
  }

  function updateShuttleFormState() {
    if (addShuttleStockBtn) {
      addShuttleStockBtn.disabled = !(shuttlePurchaseDate?.value && Number(shuttleBoxesBought?.value) >= 1);
    }
    if (recordShuttleTakenBtn) {
      recordShuttleTakenBtn.disabled = !(
        shuttleTakenDate?.value &&
        shuttleTakenTime?.value &&
        shuttleTakenBy?.value.trim() &&
        Number(shuttleTakenQuantity?.value) >= 1
      );
    }
  }

  function renderShuttleManagement() {
    const data = getShuttleData();
    const totals = getShuttleTotals(data);
    if (shuttleAvailableCount) shuttleAvailableCount.textContent = totals.available;
    if (shuttlePurchasedCount) shuttlePurchasedCount.textContent = totals.purchased;
    if (shuttleUsedCount) shuttleUsedCount.textContent = totals.used;
    if (shuttleBorrowedCount) shuttleBorrowedCount.textContent = totals.borrowed;
    renderShuttleBorrowers(data);
    renderShuttleHistory(data);
    updateShuttleFormState();
  }

  function getHomePlayersDraft() {
    return safeJsonParse(localStorage.getItem(STORAGE_KEYS.homePlayersDraft) || '[]', []);
  }

  function setHomePlayersDraft(players) {
    localStorage.setItem(STORAGE_KEYS.homePlayersDraft, JSON.stringify(players || []));
  }

  function playerPhotoKey(name) {
    return lower(normalizePlayerName(name));
  }

  function normalizePlayerPhotoMap(map) {
    const normalized = {};
    if (!map || typeof map !== 'object' || Array.isArray(map)) return normalized;
    Object.entries(map).forEach(([name, value]) => {
      const key = playerPhotoKey(name);
      if (key && typeof value === 'string' && value) normalized[key] = value;
    });
    return normalized;
  }

  function getHomePlayerPhotos() {
    return normalizePlayerPhotoMap(safeJsonParse(localStorage.getItem(STORAGE_KEYS.homePlayerPhotos) || '{}', {}));
  }

  function setHomePlayerPhotos(photos) {
    localStorage.setItem(STORAGE_KEYS.homePlayerPhotos, JSON.stringify(normalizePlayerPhotoMap(photos)));
  }

  function createKnockoutMatch(id, stage) {
    return { id, stage, team1: '', team2: '', score1: null, score2: null };
  }

  function createEmptyKnockouts() {
    return {
      semifinal1: createKnockoutMatch('SEMIFINAL-1', 'Semifinal 1'),
      semifinal2: createKnockoutMatch('SEMIFINAL-2', 'Semifinal 2'),
      qualifier1: createKnockoutMatch('QUALIFIER-1', 'Qualifier 1'),
      eliminator: createKnockoutMatch('ELIMINATOR', 'Eliminator'),
      qualifier2: createKnockoutMatch('QUALIFIER-2', 'Qualifier 2'),
      final: createKnockoutMatch('FINAL', 'Final'),
    };
  }

  function compareStandingsRows(a, b) {
    if ((b.points ?? 0) !== (a.points ?? 0)) return (b.points ?? 0) - (a.points ?? 0);
    if ((b.pd ?? 0) !== (a.pd ?? 0)) return (b.pd ?? 0) - (a.pd ?? 0);
    if ((b.pf ?? 0) !== (a.pf ?? 0)) return (b.pf ?? 0) - (a.pf ?? 0);
    return (a.team || '').localeCompare(b.team || '');
  }

  function ensureKnockoutStructure() {
    if (!tournament) return createEmptyKnockouts();
    if (!tournament.knockout) {
      tournament.knockout = createEmptyKnockouts();
    }
    return tournament.knockout;
  }

  function resetKnockoutStructure(knockout) {
    if (!knockout) return;
    clearMatchParticipants(knockout.semifinal1);
    clearMatchParticipants(knockout.semifinal2);
    clearMatchParticipants(knockout.qualifier1);
    clearMatchParticipants(knockout.eliminator);
    clearMatchParticipants(knockout.qualifier2);
    clearMatchParticipants(knockout.final);
  }

  function assignMatchParticipants(match, team1, team2) {
    if (!match) return;
    if (team1 !== undefined) {
      const normalized = team1 || '';
      if (match.team1 !== normalized) {
        match.team1 = normalized;
        match.score1 = null;
      }
    }
    if (team2 !== undefined) {
      const normalized = team2 || '';
      if (match.team2 !== normalized) {
        match.team2 = normalized;
        match.score2 = null;
      }
    }
  }

  function clearMatchParticipants(match) {
    assignMatchParticipants(match, '', '');
    if (match) {
      match.score1 = null;
      match.score2 = null;
    }
  }

  function getMatchOutcome(match) {
    if (!match) return null;
    if (!hasNumericScore(match.score1) || !hasNumericScore(match.score2)) return null;
    const s1 = Number(match.score1);
    const s2 = Number(match.score2);
    if (!Number.isFinite(s1) || !Number.isFinite(s2)) return null;
    if (s1 === s2) return null;
    if (!match.team1 || !match.team2) return null;
    if (s1 > s2) {
      return { winner: match.team1, loser: match.team2, score: [s1, s2] };
    }
    return { winner: match.team2, loser: match.team1, score: [s1, s2] };
  }

  function computeFinalResultFromKnockout(knockout) {
    if (!knockout) return null;
    const outcome = getMatchOutcome(knockout.final);
    if (!outcome) return null;
    return { winner: outcome.winner, runnerUp: outcome.loser };
  }

  function computeKnockoutSeeds(leagueStandings) {
    if (!Array.isArray(leagueStandings)) return [];
    const sorted = [...leagueStandings].sort(compareStandingsRows);
    return sorted.map(row => row.team).filter(Boolean);
  }

  function computeKnockoutParticipants(groupStandings, leagueStandings) {
    if (!tournament) {
      return [];
    }

    if (tournament.type === 'Groups') {
      if (!Array.isArray(groupStandings) || groupStandings.length === 0) {
        return [];
      }
      if (groupStandings.length === 1) {
        return computeKnockoutSeeds(groupStandings[0]);
      }

      const participants = [];
      const maxGroups = Math.min(groupStandings.length, 2);
      for (let gi = 0; gi < maxGroups; gi++) {
        const standings = groupStandings[gi] || [];
        const first = standings[0]?.team;
        const second = standings[1]?.team;
        if (first) participants.push(first);
        if (second) participants.push(second);
      }
      return participants.filter(Boolean);
    }

    return computeKnockoutSeeds(leagueStandings).slice(0, 4);
  }

  function computeDirectFinalParticipants(groupStandings, leagueStandings) {
    if (!tournament) return [];

    if (tournament.type === 'Groups' && Array.isArray(groupStandings)) {
      return groupStandings
        .flat()
        .sort(compareStandingsRows)
        .slice(0, 2)
        .map(row => row.team)
        .filter(Boolean);
    }

    return computeKnockoutSeeds(leagueStandings).slice(0, 2);
  }

  function getActivePlayersList() {
    return tournament ? (tournament.players || []) : getHomePlayersDraft();
  }

  function getActivePlayerPhotos() {
    if (tournament) {
      tournament.playerPhotos = normalizePlayerPhotoMap(tournament.playerPhotos || {});
      return tournament.playerPhotos;
    }
    return getHomePlayerPhotos();
  }

  function setActivePlayerPhotos(photos) {
    const nextPhotos = normalizePlayerPhotoMap(photos);
    setHomePlayerPhotos(nextPhotos);
    if (tournament) {
      tournament.playerPhotos = nextPhotos;
      saveTournamentAndRefresh();
    }
  }

  function getPlayerPhoto(playerName, sourceTournament = tournament) {
    const key = playerPhotoKey(playerName);
    if (!key) return '';
    const tournamentPhotos = normalizePlayerPhotoMap(sourceTournament?.playerPhotos || {});
    const homePhotos = getHomePlayerPhotos();
    if (tournamentPhotos[key] || homePhotos[key]) return tournamentPhotos[key] || homePhotos[key];

    const savedTournament = getAllSavedTournaments()
      .find(saved => normalizePlayerPhotoMap(saved?.playerPhotos || {})[key]);
    return savedTournament ? normalizePlayerPhotoMap(savedTournament.playerPhotos || {})[key] || '' : '';
  }

  function setPlayerPhoto(playerName, dataUrl) {
    const key = playerPhotoKey(playerName);
    if (!key) return;
    const photos = getActivePlayerPhotos();
    if (dataUrl) photos[key] = dataUrl;
    else delete photos[key];
    setActivePlayerPhotos(photos);
  }

  function getPlayersForTeamAssignment() {
    if (tournament && Array.isArray(tournament.players) && tournament.players.length > 0) {
      return [...tournament.players];
    }
    const draft = getHomePlayersDraft();
    return Array.isArray(draft) ? [...draft] : [];
  }

  function setActivePlayersList(players) {
    const nextPlayers = [...(players || [])];
    setHomePlayersDraft(nextPlayers);
    if (tournament) {
      tournament.players = nextPlayers;
      tournament.playerPhotos = normalizePlayerPhotoMap(tournament.playerPhotos || {});
      saveTournamentAndRefresh();
    }
  }

  function updateSavePlayerListBtnState() {
    const list = getActivePlayersList();
    savePlayerListBtn.disabled = !list || list.length === 0;
  }

  function getIndex(key) {
    return safeJsonParse(localStorage.getItem(key) || '[]', []);
  }

  function setIndex(key, arr) {
    localStorage.setItem(key, JSON.stringify(arr));
  }

  function saveTournament() {
    if (!tournament) return;
    syncVisibleScoreInputsToTournament();
    localStorage.setItem(STORAGE_KEYS.tournamentPrefix + tournament.id, JSON.stringify(tournament));
    localStorage.setItem(STORAGE_KEYS.activeTournamentId, tournament.id);
    const visibleScoreRows = collectVisibleScoreRows();
    if (visibleScoreRows.length > 0) {
      window.btAuth?.syncScoreRows?.(tournament.id, visibleScoreRows);
    } else {
      window.btAuth?.syncTournamentScores?.(tournament);
    }
  }

  function upsertTournamentIndex(t) {
    const idx = getIndex(STORAGE_KEYS.tournamentsIndex);
    const existing = idx.find(x => x.id === t.id);
    if (existing) {
      existing.name = t.name;
      existing.scheduledDate = t.scheduledDate || existing.scheduledDate || '';
      existing.ownerUsername = t.ownerUsername || existing.ownerUsername || '';
      if (!existing.createdAt) existing.createdAt = existing.updatedAt || Date.now();
      existing.updatedAt = Date.now();
    } else {
      idx.push({ id: t.id, name: t.name, ownerUsername: t.ownerUsername || '', scheduledDate: t.scheduledDate || '', createdAt: Date.now(), updatedAt: Date.now() });
    }
    idx.sort((a,b) => b.updatedAt - a.updatedAt);
    setIndex(STORAGE_KEYS.tournamentsIndex, idx);
  }

  function deleteTournamentById(id) {
    localStorage.removeItem(STORAGE_KEYS.tournamentPrefix + id);
    const idx = getIndex(STORAGE_KEYS.tournamentsIndex).filter(x => x.id !== id);
    setIndex(STORAGE_KEYS.tournamentsIndex, idx);
    if (localStorage.getItem(STORAGE_KEYS.activeTournamentId) === id) {
      localStorage.removeItem(STORAGE_KEYS.activeTournamentId);
      localStorage.removeItem(STORAGE_KEYS.activeView);
    }
    if (localStorage.getItem(STORAGE_KEYS.historyTournamentId) === id) {
      localStorage.removeItem(STORAGE_KEYS.historyTournamentId);
    }
  }

  function loadTournamentById(id) {
    const data = localStorage.getItem(STORAGE_KEYS.tournamentPrefix + id);
    if (!data) return null;
    return safeJsonParse(data, null);
  }

  function upsertPlayerList(name, players) {
    const listId = lower(name);
    localStorage.setItem(STORAGE_KEYS.playerListPrefix + listId, JSON.stringify({
      name,
      players,
      playerPhotos: getActivePlayerPhotos(),
    }));
    const idx = getIndex(STORAGE_KEYS.playerListsIndex);
    if (!idx.includes(listId)) idx.push(listId);
    idx.sort();
    setIndex(STORAGE_KEYS.playerListsIndex, idx);
  }

  function loadPlayerList(listId) {
    const data = localStorage.getItem(STORAGE_KEYS.playerListPrefix + listId);
    if (!data) return null;
    return safeJsonParse(data, null);
  }

  function ownerSuffix(ownerUsername) {
    const session = currentAuthSession();
    const owner = String(ownerUsername || '').trim();
    return session?.isAdmin && owner ? ` (${owner})` : '';
  }

  function refreshHomeDropdowns() {
    // tournaments
    const idx = getIndex(STORAGE_KEYS.tournamentsIndex);
    loadTournamentSelect.innerHTML = '<option value="">-- Select --</option>';
    idx.forEach(x => {
      const opt = document.createElement('option');
      opt.value = x.id;
      opt.textContent = `${x.name}${ownerSuffix(x.ownerUsername)}`;
      loadTournamentSelect.appendChild(opt);
    });
    loadTournamentBtn.disabled = !loadTournamentSelect.value;
    deleteTournamentBtn.disabled = !loadTournamentSelect.value;

    // player lists
    const pIdx = getIndex(STORAGE_KEYS.playerListsIndex);
    playerListSelect.innerHTML = '<option value="">-- Select --</option>';
    pIdx.forEach(id => {
      const pl = loadPlayerList(id);
      if (!pl) return;
      const opt = document.createElement('option');
      opt.value = id;
      opt.textContent = `${pl.name}${ownerSuffix(pl.ownerUsername)}`;
      playerListSelect.appendChild(opt);
    });
    loadPlayerListBtn.disabled = !playerListSelect.value;
  }

  // ----------------------------
  // Global leaderboard (across tournaments)
  // Each team accumulates:
  // - totalPoints: winner=10, runner-up=5, unqualified/non-playoff=2 per completed tournament
  // - winnerCount / runnerUpCount
  // - gamesPlayed: total league/group matches played across tournaments
  // - daysPlayed: tournaments participated in (1 per tournament where team had >=1 match)
  // ----------------------------
  /**
   * Global leaderboard is PLAYER-based.
   * - Singles: each team has 1 player → player accumulates stats.
   * - Doubles: each team has 2 players → BOTH players get full team stats.
   *
   * We recompute from *all saved tournaments* to stay correct even if users edit
   * finals, reset tournaments, or delete tournaments.
   */
  function ensureGlobalPlayerRow(board, player) {
    const key = normalizePlayerName(player);
    if (!key) return null;
    if (!board[key]) {
      board[key] = {
        player: key,
        totalPoints: 0,
        winnerCount: 0,
        runnerUpCount: 0,
        gamesPlayed: 0,
        daysPlayed: 0,
      };
    }
    return board[key];
  }

  function getAllSavedTournaments() {
    const idx = getIndex(STORAGE_KEYS.tournamentsIndex);
    return idx
      .map(x => loadTournamentById(x.id))
      .filter(Boolean);
  }

  function getPlayoffParticipantTeams(t) {
    const participants = new Set();
    const knockout = t?.knockout || {};
    [
      knockout.semifinal1,
      knockout.semifinal2,
      knockout.qualifier1,
      knockout.eliminator,
      knockout.qualifier2,
      knockout.final,
      t?.finalMatch,
    ].forEach(match => {
      if (!match) return;
      if (match.team1) participants.add(match.team1);
      if (match.team2) participants.add(match.team2);
    });
    return participants;
  }

  function computeTournamentContribution(t) {
    if (!t) return null;

    const teams = t.teams || [];
    const teamPlayers = t.teamPlayers || {};
    const standings = computeStandingsFromMatches(teams, t.matches || []);

    const playedMap = new Map(standings.map(s => [s.team, s.played]));
    const finalResult = t.finalResult || computeFinalResultFromFinalMatch(t.finalMatch);
    const playoffParticipants = getPlayoffParticipantTeams(t);

    // Build player contribution by applying per-team contributions to the mapped players.
    const perPlayer = {};
    teams.forEach(team => {
      const assigned = (teamPlayers[team] || []).map(normalizePlayerName).filter(Boolean);
      if (assigned.length === 0) return;

      const gamesPlayed = playedMap.get(team) ?? 0;
      const daysPlayed = gamesPlayed > 0 ? 1 : 0;

      const isWinner = finalResult?.winner === team;
      const isRunner = finalResult?.runnerUp === team;
      const playedDayPoints = daysPlayed > 0 && !isWinner && !isRunner ? 2 : 0;
      const totalPoints = (isWinner ? 10 : 0) + (isRunner ? 5 : 0) + playedDayPoints;

      assigned.forEach(player => {
        if (!perPlayer[player]) {
          perPlayer[player] = {
            player,
            totalPoints: 0,
            winnerCount: 0,
            runnerUpCount: 0,
            gamesPlayed: 0,
            daysPlayed: 0,
          };
        }
        perPlayer[player].totalPoints += totalPoints;
        perPlayer[player].winnerCount += isWinner ? 1 : 0;
        perPlayer[player].runnerUpCount += isRunner ? 1 : 0;
        perPlayer[player].gamesPlayed += gamesPlayed;
        perPlayer[player].daysPlayed += daysPlayed;
      });
    });

    return perPlayer;
  }

  function computeGlobalLeaderboardBoardFromAllTournaments() {
    const board = {};
    const tournaments = getAllSavedTournaments();
    tournaments.forEach(t => {
      const contrib = computeTournamentContribution(t);
      if (!contrib) return;
      Object.values(contrib).forEach(row => {
        const r = ensureGlobalPlayerRow(board, row.player);
        if (!r) return;
        r.totalPoints += row.totalPoints;
        r.winnerCount += row.winnerCount;
        r.runnerUpCount += row.runnerUpCount;
        r.gamesPlayed += row.gamesPlayed;
        r.daysPlayed += row.daysPlayed;
      });
    });
    return board;
  }

  function currentLeaderboardPeriod() {
    const now = new Date();
    return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
  }

  function selectedLeaderboardPeriod() {
    const fromControls = leaderboardMonthSelect?.value === 'all'
      ? 'all'
      : leaderboardYearSelect?.value && leaderboardMonthSelect?.value
        ? `${leaderboardYearSelect.value}-${leaderboardMonthSelect.value}`
        : '';
    return fromControls || localStorage.getItem(STORAGE_KEYS.leaderboardPeriod) || currentLeaderboardPeriod();
  }

  function leaderboardTournamentPeriod(t) {
    const entry = getIndex(STORAGE_KEYS.tournamentsIndex).find(item => item.id === t?.id);
    const dateKey = entry?.scheduledDate || t?.scheduledDate || historyDateKeyFromTimestamp(entry?.createdAt || entry?.updatedAt);
    return dateKey ? dateKey.slice(0, 7) : '';
  }

  function getLeaderboardTournaments(period = selectedLeaderboardPeriod()) {
    const tournaments = getAllSavedTournaments();
    return period === 'all' ? tournaments : tournaments.filter(t => leaderboardTournamentPeriod(t) === period);
  }

  function refreshLeaderboardPeriodControls() {
    if (!leaderboardYearSelect || !leaderboardMonthSelect) return;
    const currentPeriod = currentLeaderboardPeriod();
    const currentYear = Number(currentPeriod.slice(0, 4));
    const years = new Set([currentYear]);
    getAllSavedTournaments().forEach(t => {
      const period = leaderboardTournamentPeriod(t);
      if (period) years.add(Number(period.slice(0, 4)));
    });

    if (!leaderboardControlsInitialized) {
      const lastCurrentMonth = localStorage.getItem(STORAGE_KEYS.leaderboardCurrentMonth) || '';
      if (lastCurrentMonth !== currentPeriod) {
        localStorage.setItem(STORAGE_KEYS.leaderboardPeriod, currentPeriod);
        localStorage.setItem(STORAGE_KEYS.leaderboardCurrentMonth, currentPeriod);
      }
      leaderboardControlsInitialized = true;
    }

    const selected = localStorage.getItem(STORAGE_KEYS.leaderboardPeriod) || currentPeriod;
    if (selected !== 'all') years.add(Number(selected.slice(0, 4)));
    leaderboardYearSelect.innerHTML = '';
    [...years].filter(Number.isFinite).sort((a, b) => b - a).forEach(year => {
      const option = document.createElement('option');
      option.value = year;
      option.textContent = year;
      leaderboardYearSelect.appendChild(option);
    });
    leaderboardYearSelect.value = selected === 'all' ? String(currentYear) : selected.slice(0, 4);
    leaderboardMonthSelect.value = selected === 'all' ? 'all' : selected.slice(5, 7);
    leaderboardYearSelect.disabled = selected === 'all';
  }

  function computeGlobalLeaderboardRows() {
    const board = {};
    const tournaments = getLeaderboardTournaments();
    tournaments.forEach(t => {
      const contribution = computeTournamentContribution(t);
      if (!contribution) return;
      Object.values(contribution).forEach(row => {
        const total = ensureGlobalPlayerRow(board, row.player);
        if (!total) return;
        total.totalPoints += row.totalPoints;
        total.winnerCount += row.winnerCount;
        total.runnerUpCount += row.runnerUpCount;
        total.gamesPlayed += row.gamesPlayed;
        total.daysPlayed += row.daysPlayed;
      });
    });
    return Object.values(board).sort((a, b) => {
      if (b.totalPoints !== a.totalPoints) return b.totalPoints - a.totalPoints;
      if (b.winnerCount !== a.winnerCount) return b.winnerCount - a.winnerCount;
      if (b.runnerUpCount !== a.runnerUpCount) return b.runnerUpCount - a.runnerUpCount;
      if (b.gamesPlayed !== a.gamesPlayed) return b.gamesPlayed - a.gamesPlayed;
      if (b.daysPlayed !== a.daysPlayed) return b.daysPlayed - a.daysPlayed;
      return a.player.localeCompare(b.player);
    });
  }

  function openPlayerStatisticsForPlayer(player) {
    const normalized = normalizePlayerName(player);
    if (!normalized) return;
    localStorage.setItem(STORAGE_KEYS.playerStatsPlayer, normalized);
    if (playerStatsSelect) playerStatsSelect.value = normalized;
    setView('stats');
  }

  function getLeaderboardRankLabel(index) {
    if (index === 0) return '🥇';
    if (index === 1) return '🥈';
    if (index === 2) return '🥉';
    return index + 1;
  }

  function getMonthlyRankLabel(index) {
    if (index === 0) return '\u{1F947}';
    if (index === 1) return '\u{1F948}';
    if (index === 2) return '\u{1F949}';
    return index + 1;
  }

  function renderGlobalLeaderboard(rows) {
    finalLeaderboardOutput.innerHTML = '';
    const period = selectedLeaderboardPeriod();
    const periodDate = period === 'all' ? null : historyDateFromKey(`${period}-01`);
    const periodLabel = period === 'all'
      ? 'All Time'
      : periodDate
        ? periodDate.toLocaleDateString(undefined, { month: 'long', year: 'numeric' })
        : period;
    const tournamentCount = getLeaderboardTournaments(period).length;
    if (leaderboardPeriodSummary) {
      leaderboardPeriodSummary.textContent = `${periodLabel} · ${tournamentCount} tournament${tournamentCount === 1 ? '' : 's'} · Original points and ranking rules`;
    }
    if (!rows || rows.length === 0) {
      finalLeaderboardOutput.innerHTML = `<div class="hint">No leaderboard results recorded for ${periodLabel}.</div>`;
      return;
    }

    const table = document.createElement('table');
    const thead = document.createElement('thead');
    const trh = document.createElement('tr');
    ['Pos', 'Player', 'Total Points', 'Titles (Winner)', 'Runner-up', 'Games Played', 'Days Played'].forEach(h => {
      const th = document.createElement('th');
      th.scope = 'col';
      th.textContent = h;
      trh.appendChild(th);
    });
    thead.appendChild(trh);
    table.appendChild(thead);

    const tbody = document.createElement('tbody');
    rows.forEach((r, i) => {
      const tr = document.createElement('tr');
      const cells = [
        getMonthlyRankLabel(i),
        r.player,
        r.totalPoints,
        r.winnerCount,
        r.runnerUpCount,
        r.gamesPlayed,
        r.daysPlayed,
      ];
      cells.forEach((c, cellIndex) => {
        const td = document.createElement('td');
        if (cellIndex === 1) {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'leaderboardPlayerLink';
          btn.appendChild(createTeamPlayerTile(c));
          btn.addEventListener('click', () => openPlayerStatisticsForPlayer(c));
          td.appendChild(btn);
        } else {
          td.textContent = c;
        }
        tr.appendChild(td);
      });
      tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    finalLeaderboardOutput.appendChild(table);
  }


  // ----------------------------
  // Player statistics
  // ----------------------------
  function addKnownPlayerName(names, player) {
    const normalized = normalizePlayerName(player);
    if (!normalized) return;
    const key = lower(normalized);
    if (!names.has(key)) names.set(key, normalized);
  }

  function getAllKnownPlayerNames() {
    const names = new Map();
    getAllSavedTournaments().forEach(t => {
      (t.players || []).forEach(player => addKnownPlayerName(names, player));
      Object.values(t.teamPlayers || {}).forEach(players => {
        (players || []).forEach(player => addKnownPlayerName(names, player));
      });
    });
    return [...names.values()].sort((a, b) => a.localeCompare(b));
  }

  function getTournamentTeams(t) {
    return [...new Set([...(t?.teams || []), ...Object.keys(t?.teamPlayers || {})])];
  }

  function getTeamPlayersForStats(t, team) {
    return (t?.teamPlayers?.[team] || []).map(normalizePlayerName).filter(Boolean);
  }

  function getPlayerTeamsForStats(t, playerLower) {
    return getTournamentTeams(t).filter(team => {
      return getTeamPlayersForStats(t, team).some(player => lower(player) === playerLower);
    });
  }

  function hasNumericScore(value) {
    if (value === null || value === undefined || value === '') return false;
    return Number.isFinite(Number(value));
  }

  function getAllScoredMatchesForStats(t) {
    const seen = new Set();
    const matches = [];
    [...(t?.matches || []), ...getHistoricalKnockoutMatches(t)].forEach(match => {
      if (!match || !match.team1 || !match.team2) return;
      if (!hasNumericScore(match.score1) || !hasNumericScore(match.score2)) return;
      const key = [match.id || '', match.stage || '', match.team1, match.team2].join('||');
      if (seen.has(key)) return;
      seen.add(key);
      matches.push(match);
    });
    return matches;
  }

  function getPlayerContributionForStats(t, playerLower) {
    const contributions = computeTournamentContribution(t) || {};
    return Object.values(contributions).find(row => lower(row.player) === playerLower) || null;
  }

  function getTournamentEntryForStats(id) {
    return getIndex(STORAGE_KEYS.tournamentsIndex).find(item => item.id === id) || null;
  }

  function incrementCount(map, key) {
    const normalized = normalizePlayerName(key);
    if (!normalized) return;
    map[normalized] = (map[normalized] || 0) + 1;
  }

  function computePlayerTournamentStats(t, playerLower) {
    if (!t) return null;

    const teams = getPlayerTeamsForStats(t, playerLower);
    const rostered = teams.length > 0 || (t.players || []).some(player => lower(normalizePlayerName(player)) === playerLower);
    if (!rostered) return null;

    const teamSet = new Set(teams);
    const finalResult = getHistoricalFinalResult(t);
    const contribution = getPlayerContributionForStats(t, playerLower);
    const summary = {
      id: t.id,
      name: t.name || 'Saved Tournament',
      updatedAt: getTournamentEntryForStats(t.id)?.updatedAt || 0,
      teams,
      matchesPlayed: 0,
      wins: 0,
      losses: 0,
      ties: 0,
      pointsFor: 0,
      pointsAgainst: 0,
      rankPoints: contribution?.totalPoints || 0,
      titles: contribution?.winnerCount || 0,
      runnerUps: contribution?.runnerUpCount || 0,
      teammates: {},
      opponents: {},
    };

    teams.forEach(team => {
      getTeamPlayersForStats(t, team).forEach(player => {
        if (lower(player) !== playerLower) incrementCount(summary.teammates, player);
      });
    });

    getAllScoredMatchesForStats(t).forEach(match => {
      const playerTeam = teamSet.has(match.team1) ? match.team1 : teamSet.has(match.team2) ? match.team2 : '';
      if (!playerTeam) return;

      const opponentTeam = playerTeam === match.team1 ? match.team2 : match.team1;
      const playerScore = Number(playerTeam === match.team1 ? match.score1 : match.score2);
      const opponentScore = Number(playerTeam === match.team1 ? match.score2 : match.score1);

      summary.matchesPlayed += 1;
      summary.pointsFor += playerScore;
      summary.pointsAgainst += opponentScore;
      if (playerScore > opponentScore) summary.wins += 1;
      else if (playerScore < opponentScore) summary.losses += 1;
      else summary.ties += 1;

      const opponentPlayers = getTeamPlayersForStats(t, opponentTeam);
      if (opponentPlayers.length > 0) {
        opponentPlayers.forEach(player => incrementCount(summary.opponents, player));
      } else {
        incrementCount(summary.opponents, opponentTeam);
      }
    });

    if (!summary.titles && finalResult?.winner && teams.includes(finalResult.winner)) summary.titles = 1;
    if (!summary.runnerUps && finalResult?.runnerUp && teams.includes(finalResult.runnerUp)) summary.runnerUps = 1;

    return summary;
  }

  function selectedPlayerStatsPeriod() {
    if (playerStatsMonthSelect?.value === 'all') return 'all';
    if (playerStatsYearSelect?.value && playerStatsMonthSelect?.value) {
      return `${playerStatsYearSelect.value}-${playerStatsMonthSelect.value}`;
    }
    return localStorage.getItem(STORAGE_KEYS.playerStatsPeriod) || 'all';
  }

  function playerStatsPeriodLabel(period = selectedPlayerStatsPeriod()) {
    if (period === 'all') return 'All Time';
    const date = historyDateFromKey(`${period}-01`);
    return date
      ? date.toLocaleDateString(undefined, { month: 'long', year: 'numeric' })
      : period;
  }

  function refreshPlayerStatsPeriodControls() {
    if (!playerStatsYearSelect || !playerStatsMonthSelect) return;
    const currentYear = new Date().getFullYear();
    const years = new Set([currentYear]);
    getAllSavedTournaments().forEach(t => {
      const period = leaderboardTournamentPeriod(t);
      if (period) years.add(Number(period.slice(0, 4)));
    });
    const selected = localStorage.getItem(STORAGE_KEYS.playerStatsPeriod) || 'all';
    if (selected !== 'all') years.add(Number(selected.slice(0, 4)));
    playerStatsYearSelect.innerHTML = '';
    [...years].filter(Number.isFinite).sort((a, b) => b - a).forEach(year => {
      const option = document.createElement('option');
      option.value = year;
      option.textContent = year;
      playerStatsYearSelect.appendChild(option);
    });
    playerStatsYearSelect.value = selected === 'all' ? String(currentYear) : selected.slice(0, 4);
    playerStatsMonthSelect.value = selected === 'all' ? 'all' : selected.slice(5, 7);
    playerStatsYearSelect.disabled = selected === 'all';
  }

  function computePlayerStatistics(playerName, period = selectedPlayerStatsPeriod()) {
    const player = normalizePlayerName(playerName);
    const playerLower = lower(player);
    const stats = {
      player,
      rosteredTournaments: 0,
      tournamentsPlayed: 0,
      matchesPlayed: 0,
      wins: 0,
      losses: 0,
      ties: 0,
      pointsFor: 0,
      pointsAgainst: 0,
      rankPoints: 0,
      titles: 0,
      runnerUps: 0,
      teammates: {},
      opponents: {},
      tournaments: [],
      period,
    };

    const tournaments = period === 'all'
      ? getAllSavedTournaments()
      : getAllSavedTournaments().filter(t => leaderboardTournamentPeriod(t) === period);
    tournaments.forEach(t => {
      const summary = computePlayerTournamentStats(t, playerLower);
      if (!summary) return;

      stats.rosteredTournaments += 1;
      if (summary.teams.length > 0 || summary.matchesPlayed > 0) stats.tournamentsPlayed += 1;
      stats.matchesPlayed += summary.matchesPlayed;
      stats.wins += summary.wins;
      stats.losses += summary.losses;
      stats.ties += summary.ties;
      stats.pointsFor += summary.pointsFor;
      stats.pointsAgainst += summary.pointsAgainst;
      stats.rankPoints += summary.rankPoints;
      stats.titles += summary.titles;
      stats.runnerUps += summary.runnerUps;
      Object.entries(summary.teammates).forEach(([name, count]) => { stats.teammates[name] = (stats.teammates[name] || 0) + count; });
      Object.entries(summary.opponents).forEach(([name, count]) => { stats.opponents[name] = (stats.opponents[name] || 0) + count; });
      stats.tournaments.push(summary);
    });

    stats.pointDiff = stats.pointsFor - stats.pointsAgainst;
    stats.winRate = stats.matchesPlayed > 0 ? Math.round((stats.wins / stats.matchesPlayed) * 100) : 0;
    stats.tournaments.sort((a, b) => (b.updatedAt || 0) - (a.updatedAt || 0));
    return stats;
  }

  function formatRecord(wins, losses, ties) {
    return ties > 0 ? `${wins}-${losses}-${ties}` : `${wins}-${losses}`;
  }

  function topCountRows(counts, limit) {
    return Object.entries(counts || {})
      .sort((a, b) => b[1] - a[1] || a[0].localeCompare(b[0]))
      .slice(0, limit)
      .map(([name, count]) => [name, count]);
  }

  function appendStatCard(parent, label, value, tone = '') {
    const card = document.createElement('div');
    card.className = `statCard${tone ? ` ${tone}` : ''}`;
    const valueEl = document.createElement('div');
    valueEl.className = 'statValue';
    valueEl.textContent = value;
    const labelEl = document.createElement('div');
    labelEl.className = 'statLabel';
    labelEl.textContent = label;
    card.appendChild(valueEl);
    card.appendChild(labelEl);
    parent.appendChild(card);
  }

  function createPlayerStatsSection(title, meta = '') {
    const section = document.createElement('div');
    section.className = 'playerStatsSection';
    const header = document.createElement('div');
    header.className = 'playerStatsSectionHeader';
    const heading = document.createElement('h4');
    heading.textContent = title;
    header.appendChild(heading);
    if (meta) {
      const pill = document.createElement('span');
      pill.className = 'pill';
      pill.textContent = meta;
      header.appendChild(pill);
    }
    section.appendChild(header);
    return section;
  }

  function appendPlayerRelationPanel(parent, title, rows, countLabel, emptyMessage) {
    const section = createPlayerStatsSection(title);
    if (!rows.length) {
      section.appendChild(createHistoryHint(emptyMessage));
      parent.appendChild(section);
      return;
    }
    const list = document.createElement('div');
    list.className = 'playerRelationList';
    rows.forEach(([name, count], index) => {
      const row = document.createElement('div');
      row.className = 'playerRelationRow';
      const rank = document.createElement('span');
      rank.className = 'playerRelationRank';
      rank.textContent = `#${index + 1}`;
      const player = document.createElement('div');
      player.className = 'playerRelationName';
      player.appendChild(createTeamPlayerTile(name));
      const total = document.createElement('span');
      total.className = 'playerRelationCount';
      total.textContent = `${count} ${countLabel}`;
      row.append(rank, player, total);
      list.appendChild(row);
    });
    section.appendChild(list);
    parent.appendChild(section);
  }

  function appendPlayerTournamentBreakdown(parent, stats) {
    const section = createPlayerStatsSection('Tournament Breakdown', `${stats.tournaments.length} saved`);
    const table = document.createElement('table');
    table.className = 'playerStatsTable';
    const thead = document.createElement('thead');
    const header = document.createElement('tr');
    ['Tournament', 'Team / Players', 'Result', 'Matches', 'Record', 'PF', 'PA', 'PD', 'Rank Pts'].forEach(label => {
      const th = document.createElement('th');
      th.scope = 'col';
      th.textContent = label;
      header.appendChild(th);
    });
    thead.appendChild(header);
    table.appendChild(thead);
    const tbody = document.createElement('tbody');

    stats.tournaments.forEach(row => {
      const tr = document.createElement('tr');
      const savedTournament = loadTournamentById(row.id);
      const values = [
        row.name,
        row.teams.length ? row.teams.map(team => getHistoricalTeamDisplayName(savedTournament, team)).join(', ') : '-',
      ];
      values.forEach(value => {
        const td = document.createElement('td');
        td.textContent = value;
        tr.appendChild(td);
      });

      const resultCell = document.createElement('td');
      const result = document.createElement('span');
      if (row.titles > 0) {
        result.className = 'playerResultBadge champion';
        result.textContent = 'Champion';
      } else if (row.runnerUps > 0) {
        result.className = 'playerResultBadge runner';
        result.textContent = 'Runner-up';
      } else if (row.matchesPlayed > 0) {
        result.className = 'playerResultBadge played';
        result.textContent = 'Played';
      } else {
        result.className = 'playerResultBadge registered';
        result.textContent = 'Registered';
      }
      resultCell.appendChild(result);
      tr.appendChild(resultCell);

      [
        row.matchesPlayed,
        formatRecord(row.wins, row.losses, row.ties),
        row.pointsFor,
        row.pointsAgainst,
        row.pointsFor - row.pointsAgainst,
        row.rankPoints,
      ].forEach(value => {
        const td = document.createElement('td');
        td.textContent = value;
        tr.appendChild(td);
      });
      tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    section.appendChild(table);
    parent.appendChild(section);
  }

  function renderPlayerStatistics(stats) {
    playerStatsOutput.innerHTML = '';
    if (!stats || !stats.player) {
      playerStatsOutput.appendChild(createHistoryHint('Select a player to view statistics.'));
      return;
    }

    if (stats.rosteredTournaments === 0) {
      playerStatsOutput.appendChild(createHistoryHint(
        `No tournament data is available for ${stats.player} in ${playerStatsPeriodLabel(stats.period)}.`
      ));
      return;
    }

    const hero = document.createElement('div');
    hero.className = 'playerStatsHero';
    const playerTile = createTeamPlayerTile(stats.player, 'playerStatsHeroPlayer');
    const identity = document.createElement('div');
    const heading = document.createElement('h4');
    heading.className = 'playerStatsName';
    heading.textContent = 'Player Summary';
    const meta = document.createElement('div');
    meta.className = 'playerStatsMeta';
    [
      `${stats.tournamentsPlayed} tournament${stats.tournamentsPlayed === 1 ? '' : 's'}`,
      `${stats.matchesPlayed} match${stats.matchesPlayed === 1 ? '' : 'es'}`,
      `${stats.rankPoints} rank points`,
    ].forEach(text => {
      const chip = document.createElement('span');
      chip.className = 'playerStatsChip';
      chip.textContent = text;
      meta.appendChild(chip);
    });
    identity.append(heading, meta);

    const winRate = document.createElement('div');
    winRate.className = 'playerWinRate';
    const winRateTop = document.createElement('div');
    winRateTop.className = 'playerWinRateTop';
    const winRateValue = document.createElement('span');
    winRateValue.className = 'playerWinRateValue';
    winRateValue.textContent = `${stats.winRate}%`;
    const winRateLabel = document.createElement('span');
    winRateLabel.className = 'playerWinRateLabel';
    winRateLabel.textContent = 'Win rate';
    winRateTop.append(winRateValue, winRateLabel);
    const winRateTrack = document.createElement('div');
    winRateTrack.className = 'playerWinRateTrack';
    const winRateFill = document.createElement('div');
    winRateFill.className = 'playerWinRateFill';
    winRateFill.style.width = `${Math.max(0, Math.min(100, stats.winRate))}%`;
    winRateTrack.appendChild(winRateFill);
    winRate.append(winRateTop, winRateTrack);
    hero.append(playerTile, identity, winRate);
    playerStatsOutput.appendChild(hero);

    const grid = document.createElement('div');
    grid.className = 'statGrid playerStatsGrid';
    appendStatCard(grid, 'Record', formatRecord(stats.wins, stats.losses, stats.ties));
    appendStatCard(grid, 'Points For', stats.pointsFor);
    appendStatCard(grid, 'Points Against', stats.pointsAgainst);
    appendStatCard(grid, 'Point Difference', stats.pointDiff);
    appendStatCard(grid, 'Titles', stats.titles, stats.titles > 0 ? 'highlight' : '');
    appendStatCard(grid, 'Runner-up', stats.runnerUps);
    playerStatsOutput.appendChild(grid);

    appendPlayerTournamentBreakdown(playerStatsOutput, stats);

    const split = document.createElement('div');
    split.className = 'playerStatsSplit';
    const partnerRows = topCountRows(stats.teammates, 8);
    const opponentRows = topCountRows(stats.opponents, 8);
    appendPlayerRelationPanel(split, 'Most Common Partners', partnerRows, 'teams', 'No doubles partner data yet.');
    appendPlayerRelationPanel(split, 'Most Faced Opponents', opponentRows, 'matches', 'No opponent data yet.');
    playerStatsOutput.appendChild(split);

    const note = document.createElement('div');
    note.className = 'statSubtle';
    note.style.marginTop = '12px';
    note.textContent = 'Statistics include saved league/group matches and saved finals/playoff matches with entered scores.';
    playerStatsOutput.appendChild(note);
  }

  function refreshPlayerStatsSelect() {
    if (!playerStatsSelect) return;
    const players = getAllKnownPlayerNames();
    const previous = playerStatsSelect.value || localStorage.getItem(STORAGE_KEYS.playerStatsPlayer) || '';
    playerStatsSelect.innerHTML = '<option value="">-- Select Player --</option>';

    players.forEach(player => {
      const option = document.createElement('option');
      option.value = player;
      option.textContent = player;
      playerStatsSelect.appendChild(option);
    });

    const selected = players.includes(previous) ? previous : (players[0] || '');
    playerStatsSelect.value = selected;
    if (selected) localStorage.setItem(STORAGE_KEYS.playerStatsPlayer, selected);
    else localStorage.removeItem(STORAGE_KEYS.playerStatsPlayer);
  }

  function renderSelectedPlayerStats() {
    if (!playerStatsOutput) return;
    const player = playerStatsSelect?.value || '';
    const period = selectedPlayerStatsPeriod();
    if (playerStatsPeriodSummary) {
      playerStatsPeriodSummary.textContent = `Viewing ${playerStatsPeriodLabel(period)} statistics`;
    }
    if (!player) {
      playerStatsOutput.innerHTML = '';
      playerStatsOutput.appendChild(createHistoryHint('No saved player data yet.'));
      return;
    }

    localStorage.setItem(STORAGE_KEYS.playerStatsPlayer, player);
    renderPlayerStatistics(computePlayerStatistics(player, period));
  }


  // ----------------------------
  // Historical tournament viewer
  // ----------------------------
  function createHistoryHint(message) {
    const div = document.createElement('div');
    div.className = 'hint';
    div.textContent = message;
    return div;
  }

  function appendHistoryHeading(parent, text) {
    const heading = document.createElement('h4');
    heading.textContent = text;
    heading.style.margin = '16px 0 6px';
    parent.appendChild(heading);
  }

  function appendHistoryTable(parent, headers, rows) {
    if (!rows || rows.length === 0) return false;

    const table = document.createElement('table');
    const thead = document.createElement('thead');
    const trh = document.createElement('tr');
    headers.forEach(header => {
      const th = document.createElement('th');
      th.scope = 'col';
      th.textContent = header;
      trh.appendChild(th);
    });
    thead.appendChild(trh);
    table.appendChild(thead);

    const tbody = document.createElement('tbody');
    rows.forEach(row => {
      const tr = document.createElement('tr');
      row.forEach(value => {
        const td = document.createElement('td');
        td.textContent = value === null || value === undefined || value === '' ? '-' : String(value);
        tr.appendChild(td);
      });
      tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    parent.appendChild(table);
    return true;
  }

  function formatHistoryDate(ms) {
    if (!ms) return '-';
    const date = new Date(ms);
    if (Number.isNaN(date.getTime())) return '-';
    return date.toLocaleString();
  }

  function formatHistoryShortDate(ms) {
    if (!ms) return '';
    const date = new Date(ms);
    if (Number.isNaN(date.getTime())) return '';
    return date.toLocaleDateString();
  }

  function formatHistoryScore(value) {
    return value === null || value === undefined || value === '' ? '-' : value;
  }

  function getTournamentIndexEntry(id) {
    return getIndex(STORAGE_KEYS.tournamentsIndex).find(item => item.id === id) || null;
  }

  function getHistoricalTeamDisplayName(t, team) {
    if (!team) return '-';
    const players = (t?.teamPlayers?.[team] || []).map(normalizePlayerName).filter(Boolean);
    return players.length ? players.join(' / ') : team;
  }

  function getHistoricalGroups(t) {
    const storedGroups = Array.isArray(t?.groupAssignments)
      ? t.groupAssignments
          .map(group => Array.isArray(group) ? group.filter(Boolean) : [])
          .filter(group => group.length > 0)
      : [];
    if (storedGroups.length > 0) return storedGroups;

    const groupsCount = parseInt(t?.groupsCount || '0', 10);
    const teamsPerGroup = parseInt(t?.teamsPerGroup || '0', 10);
    if (!Number.isFinite(groupsCount) || !Number.isFinite(teamsPerGroup) || groupsCount < 1 || teamsPerGroup < 1) {
      return [];
    }
    return partitionIntoGroups(t?.teams || [], groupsCount, teamsPerGroup).filter(group => group.length > 0);
  }

  function computeHistoricalStandings(t) {
    const matches = Array.isArray(t?.matches) ? t.matches : [];
    if (t?.type === 'Groups') {
      return {
        grouped: true,
        groups: getHistoricalGroups(t).map((groupTeams, index) => ({
          title: `Group ${index + 1}`,
          rows: computeStandingsForTeams(
            groupTeams,
            matches.filter(match => match.stage === 'Group' && Number(match.groupIndex) === index)
          ),
        })),
      };
    }

    return {
      grouped: false,
      rows: computeStandingsForTeams(t?.teams || [], matches.filter(match => match.stage !== 'Final')),
    };
  }

  function getHistoricalFinalResult(t) {
    return t?.finalResult
      || computeFinalResultFromFinalMatch(t?.finalMatch)
      || computeFinalResultFromFinalMatch(t?.knockout?.final);
  }

  function getHistoricalKnockoutMatches(t) {
    const knockout = t?.knockout || {};
    const matches = [
      knockout.semifinal1,
      knockout.semifinal2,
      knockout.qualifier1,
      knockout.eliminator,
      knockout.qualifier2,
      knockout.final,
    ].filter(match => match && (
      match.team1 || match.team2
      || match.score1 !== null && match.score1 !== undefined
      || match.score2 !== null && match.score2 !== undefined
    ));

    if (t?.finalMatch && !matches.some(match => match.id === t.finalMatch.id)) {
      matches.push(t.finalMatch);
    }
    return matches;
  }

  function renderHistoricalSummary(t, entry) {
    appendHistoryHeading(historyOutput, 'Summary');
    appendHistoryTable(historyOutput, ['Field', 'Value'], [
      ['Tournament', t.name || '-'],
      ['Type', t.type || '-'],
      ['Fixture', t.fixtureType || '-'],
      ['Playoff Format', t.playoffFormat || '-'],
      ['Match Type', t.matchType || '-'],
      ['Teams', (t.teams || []).length],
      ['Players', (t.players || []).length],
      ['Tournament Date', (() => {
        const date = historyDateFromKey(t.scheduledDate || entry?.scheduledDate || '');
        return date ? date.toLocaleDateString() : formatHistoryShortDate(entry?.createdAt);
      })()],
      ['Created', formatHistoryDate(entry?.createdAt)],
      ['Updated', formatHistoryDate(entry?.updatedAt)],
    ]);
  }

  function renderHistoricalFinals(t) {
    appendHistoryHeading(historyOutput, 'Finals');
    const result = getHistoricalFinalResult(t);
    if (result) {
      appendHistoryTable(historyOutput, ['Champion', 'Runner-up'], [[
        getHistoricalTeamDisplayName(t, result.winner),
        getHistoricalTeamDisplayName(t, result.runnerUp),
      ]]);
    }

    const knockoutMatches = getHistoricalKnockoutMatches(t);
    if (knockoutMatches.length === 0) {
      if (!result) historyOutput.appendChild(createHistoryHint('No finals recorded yet.'));
      return;
    }

    appendHistoryTable(historyOutput, ['Stage', 'Team 1', 'Score', 'Score', 'Team 2'], knockoutMatches.map(match => [
      match.stage || 'Final',
      getHistoricalTeamDisplayName(t, match.team1),
      formatHistoryScore(match.score1),
      formatHistoryScore(match.score2),
      getHistoricalTeamDisplayName(t, match.team2),
    ]));
  }

  function renderHistoricalStandings(t) {
    appendHistoryHeading(historyOutput, 'Standings');
    const standings = computeHistoricalStandings(t);
    const renderRows = rows => rows.map((row, index) => [
      index + 1,
      getHistoricalTeamDisplayName(t, row.team),
      row.played,
      row.won,
      row.lost,
      row.pf,
      row.pa,
      row.pd,
      row.points,
    ]);

    if (standings.grouped) {
      if (!standings.groups.length) {
        historyOutput.appendChild(createHistoryHint('No group standings yet.'));
        return;
      }
      standings.groups.forEach(group => {
        appendHistoryHeading(historyOutput, group.title);
        appendHistoryTable(historyOutput, ['Pos', 'Team Players', 'P', 'W', 'L', 'PF', 'PA', 'PD', 'Pts'], renderRows(group.rows));
      });
      return;
    }

    if (!standings.rows || standings.rows.length === 0) {
      historyOutput.appendChild(createHistoryHint('No standings yet.'));
      return;
    }
    appendHistoryTable(historyOutput, ['Pos', 'Team Players', 'P', 'W', 'L', 'PF', 'PA', 'PD', 'Pts'], renderRows(standings.rows));
  }

  function renderHistoricalSchedule(t) {
    appendHistoryHeading(historyOutput, 'Schedule');
    const matches = Array.isArray(t?.matches) ? t.matches : [];
    if (!matches.length) {
      historyOutput.appendChild(createHistoryHint('No matches recorded yet.'));
      return;
    }

    appendHistoryTable(historyOutput, ['Stage', 'Match', 'Team 1', 'Score', 'Score', 'Team 2'], matches.map((match, index) => {
      const stage = match.stage === 'Group' && match.groupIndex !== null && match.groupIndex !== undefined
        ? `Group ${Number(match.groupIndex) + 1}`
        : (match.stage || 'League');
      return [
        stage,
        match.id || index + 1,
        getHistoricalTeamDisplayName(t, match.team1),
        formatHistoryScore(match.score1),
        formatHistoryScore(match.score2),
        getHistoricalTeamDisplayName(t, match.team2),
      ];
    }));
  }

  function renderHistoricalRosters(t) {
    appendHistoryHeading(historyOutput, 'Teams');
    const teams = Array.isArray(t?.teams) ? t.teams : [];
    if (!teams.length) {
      historyOutput.appendChild(createHistoryHint('No teams saved for this tournament.'));
      return;
    }

    appendHistoryTable(historyOutput, ['Team', 'Players'], teams.map(team => [
      team,
      (t.teamPlayers?.[team] || []).map(normalizePlayerName).filter(Boolean).join(' / ') || '-',
    ]));
  }

  function renderTournamentHistory(t, entry) {
    historyOutput.innerHTML = '';
    if (!t) {
      historyOutput.appendChild(createHistoryHint('Select a saved tournament to view its history.'));
      return;
    }

    const title = document.createElement('h4');
    title.textContent = t.name || 'Saved Tournament';
    title.style.margin = '0 0 8px';
    historyOutput.appendChild(title);

    renderHistoricalSummary(t, entry);
    renderHistoricalFinals(t);
    renderHistoricalStandings(t);
    renderHistoricalSchedule(t);
    renderHistoricalRosters(t);
  }

  function historyDateKeyFromTimestamp(ms) {
    if (!ms) return '';
    const date = new Date(ms);
    if (Number.isNaN(date.getTime())) return '';
    const offset = date.getTimezoneOffset() * 60000;
    return new Date(date.getTime() - offset).toISOString().slice(0, 10);
  }

  function historyDateFromKey(key) {
    const parts = String(key || '').split('-').map(Number);
    if (parts.length !== 3 || parts.some(value => !Number.isFinite(value))) return null;
    return new Date(parts[0], parts[1] - 1, parts[2]);
  }

  function historyTournamentDateKey(item) {
    return item?.scheduledDate || historyDateKeyFromTimestamp(item?.createdAt || item?.updatedAt);
  }

  function getHistoryTournamentItems() {
    return getIndex(STORAGE_KEYS.tournamentsIndex)
      .filter(item => loadTournamentById(item.id))
      .sort((a, b) => Number(b.createdAt || b.updatedAt || 0) - Number(a.createdAt || a.updatedAt || 0));
  }

  function ensureHistoryDateSelection(items) {
    let selectedDate = localStorage.getItem(STORAGE_KEYS.historyDate) || '';
    if (!/^\d{4}-\d{2}-\d{2}$/.test(selectedDate)) {
      selectedDate = historyTournamentDateKey(items[0]) || todayForDateInput();
    }
    localStorage.setItem(STORAGE_KEYS.historyDate, selectedDate);
    if (!historyCalendarCursor) {
      const selected = historyDateFromKey(selectedDate) || new Date();
      historyCalendarCursor = new Date(selected.getFullYear(), selected.getMonth(), 1);
    }
    return selectedDate;
  }

  function renderHistoryCalendar(items = getHistoryTournamentItems()) {
    if (!historyCalendarGrid || !historyCalendarMonth) return;
    const selectedDate = ensureHistoryDateSelection(items);
    const cursor = historyCalendarCursor || new Date();
    const year = cursor.getFullYear();
    const month = cursor.getMonth();
    historyCalendarMonth.textContent = cursor.toLocaleDateString(undefined, { month: 'long', year: 'numeric' });
    historyCalendarGrid.innerHTML = '';

    const counts = new Map();
    items.forEach(item => {
      const key = historyTournamentDateKey(item);
      if (key) counts.set(key, (counts.get(key) || 0) + 1);
    });

    const firstWeekday = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    for (let index = 0; index < firstWeekday; index += 1) {
      const blank = document.createElement('span');
      blank.className = 'historyCalendarBlank';
      blank.setAttribute('aria-hidden', 'true');
      historyCalendarGrid.appendChild(blank);
    }

    for (let day = 1; day <= daysInMonth; day += 1) {
      const date = new Date(year, month, day);
      const key = historyDateKeyFromTimestamp(date.getTime());
      const count = counts.get(key) || 0;
      const button = document.createElement('button');
      button.type = 'button';
      button.className = `historyCalendarDay${count ? ' hasTournaments' : ''}${key === selectedDate ? ' selected' : ''}`;
      button.textContent = day;
      button.setAttribute('role', 'gridcell');
      button.setAttribute('aria-label', `${date.toLocaleDateString()}${count ? `, ${count} tournament${count === 1 ? '' : 's'}` : ', no tournaments'}`);
      button.title = count ? `${count} tournament${count === 1 ? '' : 's'}` : 'No tournaments';
      button.addEventListener('click', () => {
        localStorage.setItem(STORAGE_KEYS.historyDate, key);
        if (historyScheduleStatus) setAuthStatus(historyScheduleStatus, '');
        refreshHistoryDropdown();
        renderSelectedHistoryTournament();
      });
      historyCalendarGrid.appendChild(button);
    }

    const selectedCount = counts.get(selectedDate) || 0;
    const selected = historyDateFromKey(selectedDate);
    if (historyCalendarStatus) {
      historyCalendarStatus.textContent = selected
        ? `${selectedCount} tournament${selectedCount === 1 ? '' : 's'} on ${selected.toLocaleDateString()}`
        : '';
    }
  }

  function refreshHistoryDropdown() {
    if (!historyTournamentSelect) return;
    const items = getHistoryTournamentItems();
    const selectedDate = ensureHistoryDateSelection(items);
    const selectedDateValue = historyDateFromKey(selectedDate);
    if (historyScheduleDateLabel) {
      historyScheduleDateLabel.textContent = selectedDateValue ? selectedDateValue.toLocaleDateString() : 'Select a date';
    }
    if (historyScheduleTournamentBtn) {
      historyScheduleTournamentBtn.disabled = !(selectedDate && historyScheduledTournamentName?.value.trim());
    }
    const filtered = items.filter(item => historyTournamentDateKey(item) === selectedDate);
    const previous = historyTournamentSelect.value || localStorage.getItem(STORAGE_KEYS.historyTournamentId) || '';
    historyTournamentSelect.innerHTML = '<option value="">-- Select Tournament --</option>';

    filtered.forEach(item => {
      const option = document.createElement('option');
      const timestamp = item.createdAt || item.updatedAt;
      const time = timestamp ? new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
      option.value = item.id;
      option.textContent = time ? `${item.name} (${time})` : item.name;
      historyTournamentSelect.appendChild(option);
    });

    const selected = filtered.some(item => item.id === previous) ? previous : (filtered[0]?.id || '');
    historyTournamentSelect.value = selected;
    historyTournamentSelect.disabled = filtered.length === 0;
    if (selected) localStorage.setItem(STORAGE_KEYS.historyTournamentId, selected);
    else localStorage.removeItem(STORAGE_KEYS.historyTournamentId);
    renderHistoryCalendar(items);
  }

  function renderSelectedHistoryTournament() {
    if (!historyOutput) return;
    const id = historyTournamentSelect?.value || '';
    if (!id) {
      historyOutput.innerHTML = '';
      const selectedDate = historyDateFromKey(localStorage.getItem(STORAGE_KEYS.historyDate) || '');
      historyOutput.appendChild(createHistoryHint(
        selectedDate
          ? `No tournaments found on ${selectedDate.toLocaleDateString()}. Select another date in the calendar.`
          : 'No saved tournaments yet.'
      ));
      return;
    }

    localStorage.setItem(STORAGE_KEYS.historyTournamentId, id);
    renderTournamentHistory(loadTournamentById(id), getTournamentIndexEntry(id));
  }

  // No nested home tabs now; bottom tabs drive navigation.

  function clearOutputs() {
    scheduleOutput.innerHTML = '';
    playoffBracketOutput.innerHTML = '';
    finalMatchOutput.innerHTML = '';
    if (scheduleFinalsArea) {
      scheduleFinalsArea.classList.add('hidden');
      scheduleFinalsArea.hidden = true;
    }
    pointsTableOutput.innerHTML = '';
    finalLeaderboardOutput.innerHTML = '';
  }

  // ----------------------------
  // Player list UI
  // ----------------------------
  function renderPlayersList() {
    playersListDiv.innerHTML = '';

    const players = getActivePlayersList();
    if (playersRosterCount) {
      playersRosterCount.textContent = `${players.length} ${players.length === 1 ? 'Player' : 'Players'}`;
    }
    if (players.length === 0) {
      playersListDiv.innerHTML = '<div class="hint">No players added yet.</div>';
      return;
    }

    players.forEach((player, idx) => {
      const row = document.createElement('article');
      row.className = 'playerCard';

      const avatar = document.createElement('div');
      avatar.className = 'playerAvatar';
      const photo = getPlayerPhoto(player);
      if (photo) {
        const img = document.createElement('img');
        img.src = photo;
        img.alt = '';
        avatar.appendChild(img);
      } else {
        avatar.textContent = playerInitials(player);
      }

      const body = document.createElement('div');
      body.className = 'playerCardBody';

      const displayName = document.createElement('div');
      displayName.className = 'playerDisplayName';
      displayName.textContent = player;

      const nameInput = document.createElement('input');
      nameInput.type = 'text';
      nameInput.className = 'playerNameInput';
      nameInput.value = player;
      nameInput.setAttribute('aria-label', `Display name for ${player}`);
      nameInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
          event.preventDefault();
          commitEdit.click();
        }
        if (event.key === 'Escape') {
          event.preventDefault();
          renderPlayersList();
        }
      });

      const actions = document.createElement('div');
      actions.className = 'playerCardActions';

      const setEditMode = (editing) => {
        row.classList.toggle('isEditing', editing);
        commitEdit.innerHTML = editing
          ? '<span aria-hidden="true">&#10003;</span><span class="srOnly">Save player name</span>'
          : '<span aria-hidden="true">&#9998;</span><span class="srOnly">Edit player details</span>';
        commitEdit.setAttribute('aria-label', editing ? `Save ${player}` : `Edit ${player}`);
        commitEdit.title = editing ? 'Save player name' : 'Edit player details';
        if (editing) {
          window.requestAnimationFrame(() => {
            nameInput.focus();
            nameInput.select();
          });
        }
      };

      const commitEdit = document.createElement('button');
      commitEdit.type = 'button';
      commitEdit.className = 'secondary playerIconBtn';
      commitEdit.addEventListener('click', () => {
        if (!row.classList.contains('isEditing')) {
          setEditMode(true);
          return;
        }
        const nextName = normalizePlayerName(nameInput.value);
        if (nextName === player) {
          nameInput.value = player;
          setEditMode(false);
          return;
        }
        renamePlayerAtIndex(idx, nextName);
      });
      setEditMode(false);

      const uploadId = `playerPhoto_${idx}_${Math.random().toString(36).slice(2)}`;
      const upload = document.createElement('input');
      upload.id = uploadId;
      upload.type = 'file';
      upload.accept = 'image/*';
      upload.className = 'photoUploadInput';
      upload.addEventListener('change', async () => {
        const file = upload.files?.[0];
        if (!file) return;
        try {
          const dataUrl = await readPlayerPhotoFile(file);
          setPlayerPhoto(player, dataUrl);
          renderPlayersList();
          renderTeamsPreview();
          recalcAndRender();
        } catch (error) {
          alert(error?.message || 'Could not update player photo.');
        } finally {
          upload.value = '';
        }
      });

      const uploadLabel = document.createElement('label');
      uploadLabel.className = 'photoUploadBtn';
      uploadLabel.htmlFor = uploadId;
      uploadLabel.title = photo ? 'Update photo' : 'Add photo';
      uploadLabel.setAttribute('aria-label', photo ? `Update photo for ${player}` : `Add photo for ${player}`);
      uploadLabel.innerHTML = '<span aria-hidden="true">&#128247;</span><span class="srOnly">Add or update photo</span>';

      const clearPhoto = document.createElement('button');
      clearPhoto.type = 'button';
      clearPhoto.className = 'secondary playerIconBtn';
      clearPhoto.innerHTML = '<span aria-hidden="true">&#9003;</span><span class="srOnly">Clear photo</span>';
      clearPhoto.setAttribute('aria-label', `Clear photo for ${player}`);
      clearPhoto.title = 'Clear photo';
      clearPhoto.disabled = !photo;
      clearPhoto.addEventListener('click', () => {
        setPlayerPhoto(player, '');
        renderPlayersList();
        recalcAndRender();
      });

      const del = document.createElement('button');
      del.className = 'playerIconBtn playerRemoveBtn';
      del.type = 'button';
      del.textContent = '\u00d7';
      del.setAttribute('aria-label', `Remove ${player}`);
      del.title = `Remove ${player}`;
      del.addEventListener('click', () => {
        const list = [...getActivePlayersList()];
        list.splice(idx, 1);
        const photos = getActivePlayerPhotos();
        delete photos[playerPhotoKey(player)];
        setActivePlayerPhotos(photos);
        setActivePlayersList(list);
        updateSavePlayerListBtnState();
        renderPlayersList();
        if (tournament) {
          tournament.teams = [];
          tournament.teamPlayers = {};
          teamsPreviewDiv.innerHTML = '';
          updateGenerateScheduleBtn();
          updateBuildTeamsBtn();
          saveTournamentAndRefresh();
        }
      });

      actions.append(commitEdit, uploadLabel, upload, clearPhoto, del);
      body.append(displayName, nameInput);
      row.append(avatar, body, actions);
      playersListDiv.appendChild(row);
    });
  }

  function renamePlayerAtIndex(index, nextName) {
    const players = [...getActivePlayersList()];
    const previousName = players[index];
    const cleanName = normalizePlayerName(nextName);
    if (!previousName || !cleanName) {
      renderPlayersList();
      return;
    }
    if (players.some((player, playerIndex) => playerIndex !== index && lower(player) === lower(cleanName))) {
      alert('Player already exists.');
      renderPlayersList();
      return;
    }
    if (previousName === cleanName) return;

    players[index] = cleanName;
    const photos = getActivePlayerPhotos();
    const previousKey = playerPhotoKey(previousName);
    const nextKey = playerPhotoKey(cleanName);
    if (photos[previousKey] && !photos[nextKey]) {
      photos[nextKey] = photos[previousKey];
    }
    delete photos[previousKey];
    setActivePlayerPhotos(photos);

    if (tournament?.teamPlayers) {
      Object.keys(tournament.teamPlayers).forEach(team => {
        tournament.teamPlayers[team] = (tournament.teamPlayers[team] || []).map(player =>
          lower(player) === lower(previousName) ? cleanName : player
        );
      });
    }

    setActivePlayersList(players);
    renderPlayersList();
    renderTeamsPreview();
    recalcAndRender();
    updateGenerateScheduleBtn();
  }

  function validatePlayersUniqueness() {
    if (!tournament) return false;
    const set = new Set();
    for (const p of (tournament.players || [])) {
      const n = lower(normalizePlayerName(p));
      if (!n) return false;
      if (set.has(n)) return false;
      set.add(n);
    }
    return true;
  }

  function validateTeamAssignments() {
    if (!tournament) return false;
    const req = requiredPlayersPerTeam();
    if (req === 0) return false;
    const teams = tournament.teams || [];
    if (!teams.length) return false;

    const pickedLower = new Set();
    for (const team of teams) {
      if (isBotTeam(team)) continue;
      const players = (tournament.teamPlayers?.[team] || []).map(normalizePlayerName);
      if (players.length !== req) return false;
      for (const p of players) {
        if (!p) return false;
        const pl = lower(p);
        if (pickedLower.has(pl)) return false;
        pickedLower.add(pl);
      }
    }
    return true;
  }

  function requiredPlayersPerTeam() {
    if (!tournament) return 0;
    if (tournament.matchType === 'Singles') return 1;
    if (tournament.matchType === 'Doubles') return 2;
    return 0;
  }

  function getPlayersCountForCurrentTournament() {
    // Prefer the players that are actually attached to the loaded tournament.
    // If no tournament is loaded, fall back to the draft players in Home → Players.
    const list = getActivePlayersList();
    return Array.isArray(list) ? list.length : 0;
  }

  function isBotTeam(team) {
    return typeof team === 'string' && team.startsWith('BOT');
  }

  function getKnockoutRoundOf() {
    const raw = parseInt(knockoutRoundOfInput?.value || tournament?.knockoutRoundOf || '0', 10);
    return Number.isFinite(raw) ? raw : 0;
  }

  function getEffectiveKnockoutRoundOf() {
    const roundOf = getKnockoutRoundOf();
    if (roundOf < 2) return 0;
    return roundOf % 2 === 0 ? roundOf : roundOf + 1;
  }

  function getRequestedTeamsCount() {
    if (tournamentTypeSelect.value === 'Knockout' || tournament?.type === 'Knockout') {
      return getEffectiveKnockoutRoundOf();
    }
    const count = parseInt(teamsCountInput.value || tournament?.teamsCount || '0', 10);
    return Number.isFinite(count) ? count : 0;
  }

  function updateBuildTeamsBtn() {
    // Relaxed: enable "Create Teams" whenever there is a tournament,
    // a match type, and a valid teams count. We no longer block on
    // having players/uniqueness before the user can click it.
    if (!buildTeamsBtn) return;

    if (!tournament) {
      buildTeamsBtn.disabled = true;
      buildTeamsBtn.textContent = 'Build Teams';
      buildTeamsBtn.style.pointerEvents = "none";
      buildTeamsBtn.style.opacity = "0.6";
      return;
    }

    const mtOk = !!matchTypeSelect.value;
    const tc = getRequestedTeamsCount();
    const tcOk = Number.isFinite(tc) && tc >= 2;

    const req = requiredPlayersPerTeam();
    const availablePlayers = getPlayersForTeamAssignment().length;
    const botSlots = tournament?.type === 'Knockout' && getKnockoutRoundOf() % 2 === 1 ? 1 : 0;
    const humanTeams = Math.max(0, tc - botSlots);
    const hasEnoughPlayers = req > 0 ? (humanTeams * req) <= availablePlayers : true;

    buildTeamsBtn.disabled = !(mtOk && tcOk && hasEnoughPlayers);
    buildTeamsBtn.textContent = (tournament.teams || []).length > 0 ? 'Rebuild Teams' : 'Build Teams';
    buildTeamsBtn.style.pointerEvents = buildTeamsBtn.disabled ? "none" : "auto";
    buildTeamsBtn.style.opacity = buildTeamsBtn.disabled ? "0.6" : "1";
  }

  function renderTeamsPreview() {
    teamsPreviewDiv.innerHTML = "";
    if (!tournament) return;

    const teams = tournament.teams || [];
    const mapping = tournament.teamPlayers || {};

    if (!matchTypeSelect.value || getRequestedTeamsCount() < 2) {
      teamsPreviewDiv.innerHTML = "";
      return;
    }

    if (!teams.length) {
      teamsPreviewDiv.innerHTML = "";
      return;
    }

    const taken = new Set();
    teams.forEach(team => {
      (mapping[team] || []).forEach(p => {
        const n = lower(normalizePlayerName(p));
        if (n) taken.add(n);
      });
    });

    function buildPlayerSelect(team, slotIndex) {
      const sel = document.createElement("select");
      sel.style.marginBottom = "0";

      const current = (mapping[team] || [])[slotIndex] || "";

      const optEmpty = document.createElement("option");
      optEmpty.value = "";
      optEmpty.textContent = "-- Select --";
      sel.appendChild(optEmpty);

      getPlayersForTeamAssignment().forEach(p => {
        const name = normalizePlayerName(p);
        if (!name) return;
        const pickedByOther = taken.has(lower(name)) && lower(name) !== lower(current);
        if (pickedByOther) return;

        const opt = document.createElement("option");
        opt.value = name;
        opt.textContent = name;
        sel.appendChild(opt);
      });

      sel.value = current;
      sel.addEventListener("change", () => {
        const req = requiredPlayersPerTeam();
        if (!tournament.teamPlayers) tournament.teamPlayers = {};
        if (!Array.isArray(tournament.teamPlayers[team])) {
          tournament.teamPlayers[team] = new Array(req).fill("");
        }
        tournament.teamPlayers[team][slotIndex] = sel.value;
        saveTournamentAndRefresh();
        renderTeamsPreview();
        renderGroupsAssignment();
        updateGenerateScheduleBtn();
        recalcAndRender();
      });

      return sel;
    }

    const renderTeamRows = (table, groupTeams) => {
      const thead = document.createElement("thead");
      const trh = document.createElement("tr");
      const cols = tournament.matchType === "Doubles"
        ? ["Team", "Player 1", "Player 2"]
        : ["Team", "Player"];
      cols.forEach(h => {
        const th = document.createElement("th");
        th.scope = "col";
        th.textContent = h;
        trh.appendChild(th);
      });
      thead.appendChild(trh);
      table.appendChild(thead);

      const tbody = document.createElement("tbody");
      groupTeams.forEach(team => {
        const tr = document.createElement("tr");
        const tdTeam = document.createElement("td");
        tdTeam.textContent = team;
        tr.appendChild(tdTeam);

        if (isBotTeam(team)) {
          const tdBot = document.createElement("td");
          tdBot.textContent = "BOT";
          tr.appendChild(tdBot);
          if (tournament.matchType === "Doubles") {
            const tdBot2 = document.createElement("td");
            tdBot2.textContent = "BOT";
            tr.appendChild(tdBot2);
          }
        } else {
          const td1 = document.createElement("td");
          td1.appendChild(buildPlayerSelect(team, 0));
          tr.appendChild(td1);
          if (tournament.matchType === "Doubles") {
            const td2 = document.createElement("td");
            td2.appendChild(buildPlayerSelect(team, 1));
            tr.appendChild(td2);
          }
        }

        tbody.appendChild(tr);
      });
      table.appendChild(tbody);
    };

    if (tournament.type === "Groups") {
      const { groupsCount, teamsPerGroup } = getGroupConfig();
      if (!Number.isFinite(groupsCount) || groupsCount < 1 || !Number.isFinite(teamsPerGroup) || teamsPerGroup < 2) {
        teamsPreviewDiv.innerHTML = "<div class=\"hint\">Set number of groups and teams per group in Tournament, then build teams.</div>";
        return;
      }
      if ((groupsCount * teamsPerGroup) > teams.length) {
        teamsPreviewDiv.innerHTML = "<div class=\"hint\">Group slots exceed built teams. Adjust group count, teams per group, or build more teams.</div>";
        return;
      }

      const groups = ensureGroupAssignments();
      groups.forEach((groupTeams, groupIndex) => {
        const heading = document.createElement("h4");
        heading.textContent = "Group " + (groupIndex + 1);
        heading.style.marginBottom = "6px";
        teamsPreviewDiv.appendChild(heading);

        const table = document.createElement("table");
        renderTeamRows(table, groupTeams);
        teamsPreviewDiv.appendChild(table);
      });
      return;
    }

    const table = document.createElement("table");
    renderTeamRows(table, teams);
    teamsPreviewDiv.appendChild(table);
  }

  function renderGroupsAssignment() {
    if (!groupsAssignmentOutput) return;
    groupsAssignmentOutput.innerHTML = "";
    if (!tournament) return;

    if (tournament.type !== "Groups") {
      groupsAssignmentOutput.innerHTML = '<div class="hint">Select Groups as the tournament type to assign teams into groups.</div>';
      return;
    }

    const teams = tournament.teams || [];
    if (teams.length < 2) {
      groupsAssignmentOutput.innerHTML = '<div class="hint">Build teams first, then assign them into groups.</div>';
      return;
    }

    const { groupsCount, teamsPerGroup } = getGroupConfig();
    if (!Number.isFinite(groupsCount) || groupsCount < 1 || !Number.isFinite(teamsPerGroup) || teamsPerGroup < 2) {
      groupsAssignmentOutput.innerHTML = '<div class="hint">Set number of groups and teams per group in Tournament before assigning groups.</div>';
      return;
    }

    if ((groupsCount * teamsPerGroup) > teams.length) {
      groupsAssignmentOutput.innerHTML = '<div class="hint">Group slots exceed built teams. Adjust group count, teams per group, or build more teams.</div>';
      return;
    }

    const groups = ensureGroupAssignments();
    const assigned = new Set(groups.flat().filter(Boolean));

    const table = document.createElement("table");
    const thead = document.createElement("thead");
    const trh = document.createElement("tr");
    ["Group", "Slot", "Team"].forEach(label => {
      const th = document.createElement("th");
      th.textContent = label;
      trh.appendChild(th);
    });
    thead.appendChild(trh);
    table.appendChild(thead);

    const tbody = document.createElement("tbody");
    groups.forEach((group, groupIndex) => {
      group.forEach((team, slotIndex) => {
        const tr = document.createElement("tr");

        const tdGroup = document.createElement("td");
        tdGroup.textContent = "Group " + (groupIndex + 1);
        tr.appendChild(tdGroup);

        const tdSlot = document.createElement("td");
        tdSlot.textContent = String(slotIndex + 1);
        tr.appendChild(tdSlot);

        const tdTeam = document.createElement("td");
        const select = document.createElement("select");
        select.style.marginBottom = "0";

        const empty = document.createElement("option");
        empty.value = "";
        empty.textContent = "-- Select team --";
        select.appendChild(empty);

        teams.forEach(teamName => {
          const option = document.createElement("option");
          option.value = teamName;
          const displayName = getTeamDisplayName(teamName);
          option.textContent = displayName === teamName ? teamName : teamName + " - " + displayName;
          option.disabled = assigned.has(teamName) && teamName !== team;
          select.appendChild(option);
        });

        select.value = team || "";
        select.addEventListener("change", () => {
          tournament.groupAssignments = normalizeGroupAssignments(tournament.groupAssignments || []);
          tournament.groupAssignments[groupIndex][slotIndex] = select.value;
          saveTournamentAndRefresh();
          renderGroupsAssignment();
          updateGenerateScheduleBtn();
        });

        tdTeam.appendChild(select);
        tr.appendChild(tdTeam);
        tbody.appendChild(tr);
      });
    });

    table.appendChild(tbody);
    groupsAssignmentOutput.appendChild(table);

    const hint = document.createElement("div");
    hint.className = "hint";
    hint.style.marginTop = "8px";
    hint.textContent = hasCompleteGroupAssignments()
      ? "Group assignment is complete. Generate Schedule will use these groups."
      : "Select one team for every group slot before generating the schedule.";
    groupsAssignmentOutput.appendChild(hint);
  }

  function saveTournamentAndRefresh() {
    if (!tournament) return;
    upsertTournamentIndex(tournament);
    saveTournament();
    if (currentView === 'history') {
      refreshHistoryDropdown();
      renderSelectedHistoryTournament();
    }
    if (currentView === 'leaderboard') {
      refreshLeaderboardPeriodControls();
      renderGlobalLeaderboard(computeGlobalLeaderboardRows());
    }
    if (currentView === 'stats') {
      refreshPlayerStatsPeriodControls();
      refreshPlayerStatsSelect();
      renderSelectedPlayerStats();
    }
  }

  // ----------------------------
  // Scheduling
  // ----------------------------
  let matchIdCounter = 1;
  function nextMatchId() { return matchIdCounter++; }

  function makeMatch(team1, team2, meta) {
    return {
      id: nextMatchId(),
      team1,
      team2,
      score1: null,
      score2: null,
      ...meta,
    };
  }

  function buildLeagueMatches(teams, fixtureType) {
    const list = [];
    for (let i = 0; i < teams.length; i++) {
      for (let j = i + 1; j < teams.length; j++) {
        list.push(makeMatch(teams[i], teams[j], { stage: 'League', groupIndex: null }));
        if (fixtureType === 'HomeAway') {
          list.push(makeMatch(teams[j], teams[i], { stage: 'League', groupIndex: null }));
        }
      }
    }
    return list;
  }

  function buildGroupMatches(groups, fixtureType) {
    const all = [];
    groups.forEach((groupTeams, groupIndex) => {
      for (let i = 0; i < groupTeams.length; i++) {
        for (let j = i + 1; j < groupTeams.length; j++) {
          all.push(makeMatch(groupTeams[i], groupTeams[j], { stage: 'Group', groupIndex }));
          if (fixtureType === 'HomeAway') {
            all.push(makeMatch(groupTeams[j], groupTeams[i], { stage: 'Group', groupIndex }));
          }
        }
      }
    });
    return all;
  }

  function getKnockoutRoundStageName(teamsRemaining, fallbackRound) {
    if (teamsRemaining === 8) return "Quarter-Final";
    if (teamsRemaining === 4) return "Semi-Final";
    if (teamsRemaining === 2) return "Final";
    return fallbackRound ? `Round ${fallbackRound}` : "Knockout";
  }

  function buildKnockoutMatches(teams) {
    const list = [];
    const stageName = getKnockoutRoundStageName(teams.length, 1);
    for (let i = 0; i < teams.length; i += 2) {
      if (!teams[i + 1]) continue;
      list.push(makeMatch(teams[i], teams[i + 1], { stage: stageName, groupIndex: null, knockoutRound: 1 }));
    }
    return list;
  }

  function getKnockoutMatchOutcome(match) {
    if (!match || !match.team1 || !match.team2) return null;
    if (isBotTeam(match.team1) && !isBotTeam(match.team2)) return { winner: match.team2, loser: match.team1 };
    if (isBotTeam(match.team2) && !isBotTeam(match.team1)) return { winner: match.team1, loser: match.team2 };
    return getMatchOutcome(match);
  }

  function progressKnockoutBracket() {
    if (!tournament || tournament.type !== "Knockout") return;
    if (!Array.isArray(tournament.matches) || tournament.matches.length === 0) return;

    const maxExistingId = tournament.matches.reduce((max, match) => {
      const id = Number(match.id);
      return Number.isFinite(id) ? Math.max(max, id) : max;
    }, 0);
    matchIdCounter = Math.max(matchIdCounter, maxExistingId + 1);

    while (true) {
      const knockoutMatches = tournament.matches.filter(match => match.knockoutRound || match.stage === "Knockout" || match.stage === "Final");
      const rounds = knockoutMatches.reduce((map, match) => {
        const round = Number(match.knockoutRound || 1);
        if (!map.has(round)) map.set(round, []);
        map.get(round).push(match);
        return map;
      }, new Map());
      if (rounds.size === 0) return;

      const sortedRounds = [...rounds.keys()].sort((a, b) => a - b);
      let changed = false;

      for (const round of sortedRounds) {
        const currentMatches = rounds.get(round) || [];
        const outcomes = currentMatches.map(getKnockoutMatchOutcome);
        const winners = outcomes.map(outcome => outcome?.winner || "");

        if (winners.some(winner => !winner)) {
          tournament.matches = tournament.matches.filter(match => Number(match.knockoutRound || 1) <= round);
          tournament.finalMatch = null;
          tournament.finalResult = null;
          return;
        }

        if (winners.length === 1) {
          const finalMatch = currentMatches[0];
          tournament.finalMatch = {
            id: finalMatch.id,
            team1: finalMatch.team1,
            team2: finalMatch.team2,
            score1: finalMatch.score1,
            score2: finalMatch.score2,
            stage: "Final",
            groupIndex: null,
          };
          tournament.finalResult = computeFinalResultFromFinalMatch(tournament.finalMatch);
          return;
        }

        const nextRound = round + 1;
        const expectedPairs = [];
        for (let i = 0; i < winners.length; i += 2) {
          if (winners[i + 1]) expectedPairs.push([winners[i], winners[i + 1]]);
        }
        if (!expectedPairs.length) return;

        const existingNext = rounds.get(nextRound) || [];
        const sameNext = existingNext.length === expectedPairs.length
          && expectedPairs.every((pair, index) => {
            const match = existingNext[index];
            return match && match.team1 === pair[0] && match.team2 === pair[1];
          });

        if (!sameNext) {
          tournament.matches = tournament.matches.filter(match => Number(match.knockoutRound || 1) < nextRound);
          const nextStageName = getKnockoutRoundStageName(winners.length, nextRound);
          expectedPairs.forEach(pair => {
            tournament.matches.push(makeMatch(pair[0], pair[1], {
              stage: nextStageName,
              groupIndex: null,
              knockoutRound: nextRound,
            }));
          });
          tournament.finalMatch = null;
          tournament.finalResult = null;
          changed = true;
          break;
        }
      }

      if (!changed) return;
    }
  }

  function partitionIntoGroups(teams, groupsCount, teamsPerGroup) {
    const totalNeeded = groupsCount * teamsPerGroup;
    const picked = teams.slice(0, totalNeeded);
    const groups = [];
    for (let g = 0; g < groupsCount; g++) {
      groups.push(picked.slice(g * teamsPerGroup, (g + 1) * teamsPerGroup));
    }
    return groups;
  }

  function getGroupConfig() {
    const groupsCount = parseInt(groupsCountInput.value || tournament?.groupsCount || "0", 10);
    const teamsPerGroup = parseInt(teamsPerGroupInput.value || tournament?.teamsPerGroup || "0", 10);
    return { groupsCount, teamsPerGroup };
  }

  function normalizeGroupAssignments(assignments) {
    const { groupsCount, teamsPerGroup } = getGroupConfig();
    const teams = tournament?.teams || [];
    const available = new Set(teams);
    const used = new Set();
    const normalized = [];

    for (let g = 0; g < groupsCount; g++) {
      const currentGroup = Array.isArray(assignments?.[g]) ? assignments[g] : [];
      const group = [];
      for (let slot = 0; slot < teamsPerGroup; slot++) {
        const selected = currentGroup[slot] || "";
        if (available.has(selected) && !used.has(selected)) {
          group.push(selected);
          used.add(selected);
        } else {
          group.push("");
        }
      }
      normalized.push(group);
    }

    return normalized;
  }

  function autoFillGroupAssignments() {
    if (!tournament) return [];
    const { groupsCount, teamsPerGroup } = getGroupConfig();
    const teams = tournament.teams || [];
    const groups = [];
    for (let g = 0; g < groupsCount; g++) {
      const group = [];
      for (let slot = 0; slot < teamsPerGroup; slot++) {
        group.push(teams[(slot * groupsCount) + g] || "");
      }
      groups.push(group);
    }
    return groups;
  }

  function ensureGroupAssignments() {
    if (!tournament || tournament.type !== "Groups") return [];
    let groups = normalizeGroupAssignments(tournament.groupAssignments || []);
    const assigned = groups.flat().filter(Boolean).length;
    if (assigned === 0) {
      groups = normalizeGroupAssignments(autoFillGroupAssignments());
    }
    tournament.groupAssignments = groups;
    return groups;
  }

  function hasCompleteGroupAssignments() {
    if (!tournament || tournament.type !== "Groups") return true;
    const { groupsCount, teamsPerGroup } = getGroupConfig();
    if (!Number.isFinite(groupsCount) || groupsCount < 1 || !Number.isFinite(teamsPerGroup) || teamsPerGroup < 2) return false;
    const groups = normalizeGroupAssignments(tournament.groupAssignments || []);
    if (groups.length !== groupsCount) return false;
    return groups.every(group => group.length === teamsPerGroup && group.every(Boolean));
  }

  function getConfiguredGroups() {
    const groups = ensureGroupAssignments();
    if (!hasCompleteGroupAssignments()) return null;
    return groups.map(group => [...group]);
  }

  function matchKey(match) {
    return [
      match.stage || "",
      match.groupIndex === null || match.groupIndex === undefined ? "ALL" : match.groupIndex,
      match.team1 || "",
      match.team2 || "",
    ].join("||");
  }

  function sameMatchSchedule(oldMatches, newMatches) {
    if (!Array.isArray(oldMatches) || oldMatches.length !== newMatches.length) return false;
    return newMatches.every((match, index) => matchKey(match) === matchKey(oldMatches[index]));
  }

  function carryExistingScores(oldMatches, newMatches) {
    const previousByKey = new Map();
    (oldMatches || []).forEach(match => {
      previousByKey.set(matchKey(match), match);
    });

    newMatches.forEach(match => {
      const previous = previousByKey.get(matchKey(match));
      if (!previous) return;
      match.score1 = previous.score1 ?? null;
      match.score2 = previous.score2 ?? null;
    });

    return newMatches;
  }

  function scheduleSignatureFor(matches) {
    return (matches || []).map(matchKey).join(";;");
  }

  // Final match: schedule between #1 and #2 from points table
  function buildFinalMatchFromStandings(standings) {
    if (standings.length < 2) return null;
    return {
      id: 'FINAL',
      team1: standings[0].team,
      team2: standings[1].team,
      score1: null,
      score2: null,
      stage: 'Final',
      groupIndex: null,
    };
  }

  // ----------------------------
  // Points Table (winner=2, loser=0, no draws)
  // ----------------------------
  function computeStandingsForTeams(teams, matchesList) {
    const stats = {};
    teams.forEach(t => {
      stats[t] = {
        team: t,
        played: 0,
        won: 0,
        lost: 0,
        pf: 0,
        pa: 0,
        pd: 0,
        points: 0,
      };
    });

    matchesList.forEach(m => {
      if (m.stage === 'Final') return;
      if (m.score1 === null || m.score2 === null) return;
      if (!(m.team1 in stats) || !(m.team2 in stats)) return;

      const a = stats[m.team1];
      const b = stats[m.team2];

      a.played++; b.played++;
      a.pf += m.score1; a.pa += m.score2;
      b.pf += m.score2; b.pa += m.score1;

      if (m.score1 > m.score2) {
        a.won++; b.lost++;
        a.points += 2;
      } else if (m.score2 > m.score1) {
        b.won++; a.lost++;
        b.points += 2;
      } else {
        // No draws supported. If equal score entered, treat as no-result.
        // You can change this if you want 1-1 points.
        a.played--; b.played--; // rollback
        a.pf -= m.score1; a.pa -= m.score2;
        b.pf -= m.score2; b.pa -= m.score1;
      }
    });

    Object.values(stats).forEach(s => {
      s.pd = s.pf - s.pa;
    });

    const sorted = Object.values(stats).sort((x, y) => {
      if (y.points !== x.points) return y.points - x.points;
      if (y.pd !== x.pd) return y.pd - x.pd;
      if (y.pf !== x.pf) return y.pf - x.pf;
      return x.team.localeCompare(y.team);
    });

    return sorted;
  }

  function computeStandingsFromMatches(teams, matchesList) {
    return computeStandingsForTeams(teams, matchesList);
  }

  function computeLeagueStandings() {
    if (!tournament) return [];
    return computeStandingsFromMatches(tournament.teams || [], (tournament.matches || []).filter(m => m.stage !== 'Final'));
  }

  function computeGroupStandings(groupsMeta) {
    if (!tournament || !Array.isArray(groupsMeta)) return [];
    return groupsMeta.map((groupTeams, index) => {
      const groupMatches = (tournament.matches || []).filter(m => m.stage === 'Group' && m.groupIndex === index);
      return computeStandingsFromMatches(groupTeams, groupMatches);
    });
  }

  function getTeamDisplayName(team) {
    if (!tournament) return team;
    const players = (tournament.teamPlayers?.[team] || []).map(normalizePlayerName).filter(Boolean);
    if (players.length === 0) return team;
    // Singles: 1 player; Doubles: show both.
    return players.join(' / ');
  }

  function getTeamDisplayPlayers(team) {
    if (!team) return ['-'];
    const assigned = (tournament?.teamPlayers?.[team] || [])
      .map(normalizePlayerName)
      .filter(Boolean);
    return assigned.length ? assigned : [getTeamDisplayName(team)];
  }

  function createTeamPlayerTile(playerName, extraClass = '') {
    const tile = document.createElement('div');
    tile.className = 'teamPlayerTile';
    if (extraClass) tile.classList.add(extraClass);

    const avatar = document.createElement('div');
    avatar.className = 'teamPlayerAvatar';
    const photoUrl = getPlayerPhoto(playerName);
    if (photoUrl) {
      const img = document.createElement('img');
      img.src = photoUrl;
      img.alt = '';
      avatar.appendChild(img);
    } else {
      avatar.textContent = playerInitials(playerName);
    }

    const name = document.createElement('div');
    name.className = 'teamPlayerName';
    name.textContent = playerName || '-';
    tile.append(avatar, name);
    return tile;
  }

  function createTeamPhotoDisplay(team, options = {}) {
    const display = document.createElement('div');
    display.className = 'teamPhotoDisplay';
    if (options.align === 'end') display.classList.add('alignEnd');
    if (options.align === 'center') display.classList.add('alignCenter');
    if (options.compact) display.classList.add('compact');
    display.setAttribute('aria-label', getTeamDisplayName(team) || '-');
    getTeamDisplayPlayers(team).forEach(player => {
      display.appendChild(createTeamPlayerTile(player));
    });
    return display;
  }

  function appendTeamPhotoDisplay(cell, team, options = {}) {
    cell.textContent = '';
    cell.appendChild(createTeamPhotoDisplay(team, options));
  }

  function getTeamFixtures(team) {
    if (!tournament || !team) return [];
    const regularFixtures = (tournament.matches || []).filter(match =>
      match.stage !== 'Final' && (match.team1 === team || match.team2 === team)
    );
    const qualifierFixtures = tournament.playoffFormat === 'Qualifiers'
      ? [
          tournament.knockout?.qualifier1,
          tournament.knockout?.eliminator,
          tournament.knockout?.qualifier2,
        ].filter(match => match?.team1 && match?.team2 && (match.team1 === team || match.team2 === team))
      : [];
    return [...regularFixtures, ...qualifierFixtures];
  }

  function syncScheduleScoreInput(matchId, scoreSide, value) {
    [scheduleOutput, playoffBracketOutput, finalMatchOutput].forEach(container => {
      container?.querySelectorAll('input.scoreInput').forEach(input => {
        if (input.dataset.matchId === String(matchId) && input.dataset.scoreSide === String(scoreSide)) {
          input.value = value;
        }
      });
    });
  }

  function autoSaveScoreFromPointsFixture(match, team, teamScoreInput, opponentScoreInput) {
    if (!match || !team) return;
    const teamIsFirst = match.team1 === team;
    const teamSide = teamIsFirst ? 1 : 2;
    const opponentSide = teamIsFirst ? 2 : 1;
    const parseScore = value => {
      if (value === '') return null;
      const parsed = parseInt(value, 10);
      return Number.isFinite(parsed) ? parsed : null;
    };
    match[`score${teamSide}`] = parseScore(teamScoreInput.value);
    match[`score${opponentSide}`] = parseScore(opponentScoreInput.value);
    syncScheduleScoreInput(match.id, teamSide, teamScoreInput.value);
    syncScheduleScoreInput(match.id, opponentSide, opponentScoreInput.value);
    saveTournament();
    scheduleScoreRender();
  }

  function createPointsFixtureDetailRow(team, columnCount) {
    const detailRow = document.createElement('tr');
    detailRow.className = 'pointsFixtureDetail';
    const detailCell = document.createElement('td');
    detailCell.colSpan = columnCount;

    const panel = document.createElement('div');
    panel.className = 'pointsFixturePanel';
    panel.dataset.team = team;

    const header = document.createElement('div');
    header.className = 'pointsFixturePanelHeader';
    const title = document.createElement('span');
    title.textContent = `${getTeamDisplayName(team)} fixtures`;
    const summary = document.createElement('span');
    summary.className = 'pill';
    const fixtures = getTeamFixtures(team);
    const completed = fixtures.filter(match => hasNumericScore(match.score1) && hasNumericScore(match.score2)).length;
    summary.textContent = `${completed}/${fixtures.length} complete`;
    header.append(title, summary);
    panel.appendChild(header);

    if (!fixtures.length) {
      const empty = document.createElement('div');
      empty.className = 'hint';
      empty.style.marginTop = '8px';
      empty.textContent = 'No fixtures are scheduled for this team.';
      panel.appendChild(empty);
      detailCell.appendChild(panel);
      detailRow.appendChild(detailCell);
      return detailRow;
    }

    const table = document.createElement('table');
    table.className = 'pointsFixtureTable';
    const caption = document.createElement('caption');
    caption.className = 'srOnly';
    caption.textContent = `${getTeamDisplayName(team)} fixture score entry`;
    table.appendChild(caption);
    const thead = document.createElement('thead');
    const headerRow = document.createElement('tr');
    ['Match', 'Team', 'Team Score', 'Opponent Score', 'Opponent'].forEach(label => {
      const th = document.createElement('th');
      th.scope = 'col';
      th.textContent = label;
      headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);
    table.appendChild(thead);

    const tbody = document.createElement('tbody');
    fixtures.forEach(match => {
      const teamIsFirst = match.team1 === team;
      const row = document.createElement('tr');
      const matchCell = document.createElement('td');
      matchCell.textContent = match.id;
      const teamCell = document.createElement('td');
      appendTeamPhotoDisplay(teamCell, team, { compact: true });
      const opponentCell = document.createElement('td');
      appendTeamPhotoDisplay(opponentCell, teamIsFirst ? match.team2 : match.team1, { compact: true });

      const teamScoreCell = document.createElement('td');
      const teamScoreInput = document.createElement('input');
      teamScoreInput.type = 'number';
      teamScoreInput.min = '0';
      teamScoreInput.className = 'scoreInput';
      teamScoreInput.dataset.matchId = String(match.id);
      teamScoreInput.dataset.scoreSide = teamIsFirst ? '1' : '2';
      teamScoreInput.inputMode = 'numeric';
      teamScoreInput.setAttribute('aria-label', `${getTeamDisplayName(team)} score for ${match.id}`);
      const teamScore = teamIsFirst ? match.score1 : match.score2;
      teamScoreInput.value = teamScore === null || teamScore === undefined ? '' : teamScore;
      teamScoreCell.appendChild(teamScoreInput);

      const opponentScoreCell = document.createElement('td');
      const opponentScoreInput = document.createElement('input');
      opponentScoreInput.type = 'number';
      opponentScoreInput.min = '0';
      opponentScoreInput.className = 'scoreInput';
      opponentScoreInput.dataset.matchId = String(match.id);
      opponentScoreInput.dataset.scoreSide = teamIsFirst ? '2' : '1';
      opponentScoreInput.inputMode = 'numeric';
      opponentScoreInput.setAttribute('aria-label', `Opponent score for ${match.id}`);
      const opponentScore = teamIsFirst ? match.score2 : match.score1;
      opponentScoreInput.value = opponentScore === null || opponentScore === undefined ? '' : opponentScore;
      opponentScoreCell.appendChild(opponentScoreInput);

      teamScoreInput.addEventListener('input', () => {
        autoSaveScoreFromPointsFixture(match, team, teamScoreInput, opponentScoreInput);
        if ((teamScoreInput.value || '').replace(/\D/g, '').length >= 2) {
          opponentScoreInput.focus();
        }
      });
      opponentScoreInput.addEventListener('input', () => {
        autoSaveScoreFromPointsFixture(match, team, teamScoreInput, opponentScoreInput);
      });

      row.append(matchCell, teamCell, teamScoreCell, opponentScoreCell, opponentCell);
      tbody.appendChild(row);
    });
    table.appendChild(tbody);
    panel.appendChild(table);
    detailCell.appendChild(panel);
    detailRow.appendChild(detailCell);
    return detailRow;
  }

  function renderPointsTableAll(standings) {
    pointsTableOutput.innerHTML = '';
    if (!standings || standings.length === 0) {
      pointsTableOutput.innerHTML = '<div class="hint">No standings yet.</div>';
      return;
    }

    const renderTableForStandings = (title, rows) => {
      if (!rows || rows.length === 0) return;
      if (title) {
        const heading = document.createElement('h4');
        heading.textContent = title;
        pointsTableOutput.appendChild(heading);
      }

      const table = document.createElement('table');
      const caption = document.createElement('caption');
      caption.className = 'srOnly';
      caption.textContent = title ? `${title} standings` : 'Tournament standings';
      table.appendChild(caption);
      const thead = document.createElement('thead');
      const trh = document.createElement('tr');
      ['Pos', 'Team Players', 'P', 'W', 'L', 'PF', 'PA', 'PD', 'Pts'].forEach(h => {
        const th = document.createElement('th');
        th.scope = 'col';
        th.textContent = h;
        trh.appendChild(th);
      });
      thead.appendChild(trh);
      table.appendChild(thead);

      const tbody = document.createElement('tbody');
      rows.forEach((s, i) => {
        const tr = document.createElement('tr');
        const cells = [
          i + 1,
          getTeamDisplayName(s.team),
          s.played,
          s.won,
          s.lost,
          s.pf,
          s.pa,
          s.pd,
          s.points,
        ];
        cells.forEach((c, cellIndex) => {
          const td = document.createElement('td');
          if (cellIndex === 1) {
            const teamButton = document.createElement('button');
            teamButton.type = 'button';
            teamButton.className = 'pointsTeamButton';
            teamButton.appendChild(createTeamPhotoDisplay(s.team, { compact: true }));
            teamButton.setAttribute('aria-expanded', String(expandedPointsTeam === s.team));
            teamButton.setAttribute('aria-label', `${expandedPointsTeam === s.team ? 'Hide' : 'Show'} fixtures for ${c}`);
            teamButton.title = 'Show this team\'s fixtures and enter scores';
            teamButton.addEventListener('click', () => {
              expandedPointsTeam = expandedPointsTeam === s.team ? null : s.team;
              recalcAndRender();
              renderSchedule(tournament?.matches || []);
            });
            td.appendChild(teamButton);
          } else {
            td.textContent = c;
          }
          tr.appendChild(td);
        });
        tbody.appendChild(tr);
        if (expandedPointsTeam === s.team) {
          tbody.appendChild(createPointsFixtureDetailRow(s.team, cells.length));
        }
      });
      table.appendChild(tbody);
      pointsTableOutput.appendChild(table);
    };

    if (tournament && tournament.type === 'Groups' && Array.isArray(standings[0])) {
      standings.forEach((groupRows, idx) => {
        renderTableForStandings(`Group ${idx + 1}`, groupRows);
      });
    } else {
      renderTableForStandings(null, standings);
    }
  }

  // ----------------------------
  // Final Leaderboard (10/5/0)
  // ----------------------------
  function computeFinalResultFromFinalMatch(finalMatch) {
    if (!finalMatch) return null;
    if (finalMatch.score1 === null || finalMatch.score2 === null) return null;
    if (finalMatch.score1 === finalMatch.score2) return null; // no draws

    const winner = finalMatch.score1 > finalMatch.score2 ? finalMatch.team1 : finalMatch.team2;
    const runnerUp = finalMatch.score1 > finalMatch.score2 ? finalMatch.team2 : finalMatch.team1;
    return { winner, runnerUp };
  }


  // ----------------------------
  // Rendering schedule
  // ----------------------------
  function renderSchedule(matchesList) {
    scheduleOutput.innerHTML = '';
    const list = Array.isArray(matchesList) ? matchesList : [];
    if (!list.length) {
      scheduleOutput.innerHTML = '<div class="hint">No matches scheduled.</div>';
      return;
    }

    const byGroup = new Map();
    list.forEach(m => {
      const key = tournament?.type === "Knockout"
        ? `R${m.knockoutRound || 1}`
        : (tournament?.type === "Groups" && m.groupIndex !== null && m.groupIndex !== undefined)
          ? `G${m.groupIndex + 1}`
          : "ALL";
      if (!byGroup.has(key)) byGroup.set(key, []);
      byGroup.get(key).push(m);
    });

    for (const [key, groupMatches] of byGroup.entries()) {
      if (tournament?.type === "Groups" && key !== "ALL") {
        const h = document.createElement("h4");
        h.textContent = `Group ${key.slice(1)}`;
        scheduleOutput.appendChild(h);
      }
      if (tournament?.type === "Knockout" && key !== "ALL") {
        const h = document.createElement("h4");
        const roundNumber = Number(key.slice(1));
        const teamsRemaining = groupMatches.length * 2;
        h.textContent = getKnockoutRoundStageName(teamsRemaining, roundNumber);
        scheduleOutput.appendChild(h);
      }

      const table = document.createElement('table');
      table.className = 'scheduleTable';
      const caption = document.createElement('caption');
      caption.className = 'srOnly';
      caption.textContent = key === 'ALL' ? 'Scheduled games score entry' : `${key} scheduled games score entry`;
      table.appendChild(caption);
      const thead = document.createElement('thead');
      const trh = document.createElement('tr');
      ['Match', 'Team 1', 'Score', 'vs', 'Score', 'Team 2'].forEach(h => {
        const th = document.createElement('th');
        th.scope = 'col';
        th.textContent = h;
        trh.appendChild(th);
      });
      thead.appendChild(trh);
      table.appendChild(thead);

      const tbody = document.createElement('tbody');
      groupMatches.forEach(m => {
        const tr = document.createElement('tr');

        const tdId = document.createElement('td');
        tdId.textContent = m.id;
        tr.appendChild(tdId);

        const tdT1 = document.createElement('td');
        appendTeamPhotoDisplay(tdT1, m.team1, { align: 'end' });
        tr.appendChild(tdT1);

        const tdS1 = document.createElement('td');
        const i1 = document.createElement('input');
        i1.type = 'number';
        i1.min = 0;
        i1.className = 'scoreInput';
        i1.dataset.matchId = String(m.id);
        i1.dataset.scoreSide = '1';
        i1.inputMode = 'numeric';
        i1.setAttribute('aria-label', `${getTeamDisplayName(m.team1)} score for match ${m.id}`);
        i1.style.width = '70px';
        i1.value = m.score1 === null || m.score1 === undefined ? '' : m.score1;
        tdS1.appendChild(i1);
        tr.appendChild(tdS1);

        const tdSep = document.createElement('td');
        tdSep.textContent = 'vs';
        tr.appendChild(tdSep);

        const tdS2 = document.createElement('td');
        const i2 = document.createElement('input');
        i2.type = 'number';
        i2.min = 0;
        i2.className = 'scoreInput';
        i2.dataset.matchId = String(m.id);
        i2.dataset.scoreSide = '2';
        i2.inputMode = 'numeric';
        i2.setAttribute('aria-label', `${getTeamDisplayName(m.team2)} score for match ${m.id}`);
        i2.style.width = '70px';
        i2.value = m.score2 === null || m.score2 === undefined ? '' : m.score2;
        const focusSecondScoreWhenReady = () => {
          if ((i1.value || '').replace(/\D/g, '').length >= 2) i2.focus();
        };
        if (tournament?.type === "Knockout") {
          i1.addEventListener('input', () => {
            onScoreDraftInput(m.id, 1, i1.value);
            focusSecondScoreWhenReady();
          });
          i1.addEventListener('change', () => onScoreInput(m.id, 1, i1.value));
        } else {
          i1.addEventListener('input', () => {
            onScoreInput(m.id, 1, i1.value);
            focusSecondScoreWhenReady();
          });
        }
        if (tournament?.type === "Knockout") {
          i2.addEventListener('input', () => onScoreDraftInput(m.id, 2, i2.value));
          i2.addEventListener('change', () => onScoreInput(m.id, 2, i2.value));
        } else {
          i2.addEventListener('input', () => onScoreInput(m.id, 2, i2.value));
        }
        tdS2.appendChild(i2);
        tr.appendChild(tdS2);

        const tdT2 = document.createElement('td');
        appendTeamPhotoDisplay(tdT2, m.team2);
        tr.appendChild(tdT2);

        tbody.appendChild(tr);
      });

      table.appendChild(tbody);
      scheduleOutput.appendChild(table);
    }
  }

  // ----------------------------
  // Score handling & updates
  // ----------------------------
  function patchTournamentScore(match, which) {
    if (!tournament || !match || !window.btAuth?.patchMatchScore) return;
    const scoreSide = which === 'score1' ? 1 : which === 'score2' ? 2 : Number(which);
    if (scoreSide !== 1 && scoreSide !== 2) return;
    const scoreValue = scoreSide === 1 ? match.score1 : match.score2;
    window.btAuth.patchMatchScore(tournament.id, match.id, scoreSide, scoreValue);
  }

  function parseScoreInputValue(rawVal) {
    if (rawVal === '' || rawVal === null || rawVal === undefined) return null;
    const parsed = parseInt(rawVal, 10);
    return Number.isFinite(parsed) ? parsed : null;
  }

  function tournamentMatchesById(matchId) {
    if (!tournament) return [];
    const id = String(matchId);
    const matches = [];
    const add = (match) => {
      if (!match || typeof match !== 'object') return;
      if (String(match.id) !== id) return;
      if (!matches.includes(match)) matches.push(match);
    };
    (tournament.matches || []).forEach(add);
    add(tournament.finalMatch);
    const knockout = tournament.knockout || {};
    ['semifinal1', 'semifinal2', 'qualifier1', 'eliminator', 'qualifier2', 'final'].forEach((key) => add(knockout[key]));
    return matches;
  }

  function applyScoreToTournamentMatch(matchId, which, rawVal) {
    const scoreSide = which === 'score1' ? 1 : which === 'score2' ? 2 : Number(which);
    if (scoreSide !== 1 && scoreSide !== 2) return null;
    const scoreKey = scoreSide === 1 ? 'score1' : 'score2';
    const value = parseScoreInputValue(rawVal);
    const matches = tournamentMatchesById(matchId);
    matches.forEach(match => {
      match[scoreKey] = value;
    });
    return matches[0] || null;
  }

  function scoreInputIsVisible(input) {
    return !!input && !input.disabled && !input.closest('[hidden], .hidden');
  }

  function visibleScoreInputs() {
    return [...document.querySelectorAll('input.scoreInput[data-match-id][data-score-side]')]
      .filter(scoreInputIsVisible);
  }

  function focusScoreInput(input) {
    if (!input || !scoreInputIsVisible(input)) return;
    try {
      input.focus({ preventScroll: true });
    } catch {
      input.focus();
    }
  }

  function advanceScoreInputIfReady(input) {
    if (!scoreInputIsVisible(input)) return;
    if ((input.value || '').replace(/\D/g, '').length < 2) return;
    const inputs = visibleScoreInputs();
    const index = inputs.indexOf(input);
    if (index < 0 || index >= inputs.length - 1) return;
    focusScoreInput(inputs[index + 1]);
  }

  function syncVisibleScoreInputsToTournament() {
    if (!tournament) return;
    document.querySelectorAll('input.scoreInput[data-match-id][data-score-side]').forEach(input => {
      if (!scoreInputIsVisible(input)) return;
      applyScoreToTournamentMatch(input.dataset.matchId, input.dataset.scoreSide, input.value);
    });
  }

  function collectVisibleScoreRows() {
    if (!tournament) return [];
    const rowsById = new Map();
    document.querySelectorAll('input.scoreInput[data-match-id][data-score-side]').forEach(input => {
      if (!scoreInputIsVisible(input)) return;
      const matchId = String(input.dataset.matchId || '').trim();
      const scoreSide = String(input.dataset.scoreSide || '');
      const score = parseScoreInputValue(input.value);
      if (!matchId || score === null) return;
      const row = rowsById.get(matchId) || { match_id: matchId, score1: null, score2: null };
      if (scoreSide === '1') row.score1 = score;
      if (scoreSide === '2') row.score2 = score;
      rowsById.set(matchId, row);
    });
    return [...rowsById.values()].filter(row => row.score1 !== null || row.score2 !== null);
  }

  function updateMatchScore(matchId, which, rawVal) {
    if (!tournament) return null;
    return applyScoreToTournamentMatch(matchId, which, rawVal);
  }

  function getActiveScoreInputSnapshot() {
    const active = document.activeElement;
    if (!active?.matches?.('input.scoreInput[data-match-id][data-score-side]')) return null;
    return {
      matchId: active.dataset.matchId,
      scoreSide: active.dataset.scoreSide,
    };
  }

  function restoreScoreInputFocus(snapshot) {
    if (!snapshot) return;
    const target = [...document.querySelectorAll('input.scoreInput[data-match-id][data-score-side]')]
      .find(input => input.dataset.matchId === snapshot.matchId && input.dataset.scoreSide === snapshot.scoreSide);
    focusScoreInput(target);
  }

  function scheduleScoreRender() {
    window.clearTimeout(scoreRenderTimer);
    scoreRenderTimer = window.setTimeout(() => {
      scoreRenderTimer = null;
      if (!tournament) return;
      const focusSnapshot = getActiveScoreInputSnapshot();
      recalcAndRender();
      restoreScoreInputFocus(focusSnapshot);
    }, 240);
  }

  function onScoreDraftInput(matchId, which, rawVal) {
    updateMatchScore(matchId, which, rawVal);
    saveTournament();
  }

  function onScoreInput(matchId, which, rawVal) {
    const match = updateMatchScore(matchId, which, rawVal);
    if (!match) return;
    scheduleScoreRender();
  }

  document.addEventListener('input', (event) => {
    const input = event.target?.closest?.('input.scoreInput[data-match-id][data-score-side]');
    if (!input || !tournament) return;
    const match = applyScoreToTournamentMatch(input.dataset.matchId, input.dataset.scoreSide, input.value);
    patchTournamentScore(match, input.dataset.scoreSide);
    advanceScoreInputIfReady(input);
  }, true);

  document.addEventListener('change', (event) => {
    const input = event.target?.closest?.('input.scoreInput[data-match-id][data-score-side]');
    if (!input || !tournament) return;
    applyScoreToTournamentMatch(input.dataset.matchId, input.dataset.scoreSide, input.value);
    scheduleScoreRender();
    saveTournament();
  }, true);

  function getPlayoffFormatLabel(format) {
    if (format === 'Final') return 'Top 2 Final';
    if (format === 'Qualifiers') return 'Qualifier Playoffs';
    return 'Semifinals';
  }

  function setScheduleFinalsArea(visible, message = '', format = '') {
    if (!scheduleFinalsArea) return;
    scheduleFinalsArea.classList.toggle('hidden', !visible);
    scheduleFinalsArea.hidden = !visible;
    if (scheduleFinalsStatus) scheduleFinalsStatus.textContent = message;
    if (scheduleFinalsFormat) scheduleFinalsFormat.textContent = format || 'Playoffs';
  }

  function getRegularScheduleCompletion() {
    const matches = Array.isArray(tournament?.matches) ? tournament.matches : [];
    const completed = matches.filter(match => hasNumericScore(match.score1) && hasNumericScore(match.score2)).length;
    return { total: matches.length, completed, ready: matches.length > 0 && completed === matches.length };
  }

  function recalcAndRender() {
    if (!tournament) return;

    if (tournament.type === "Knockout") {
      progressKnockoutBracket();
      renderSchedule(tournament.matches || []);
      const finalMatch = tournament.finalMatch || (tournament.matches || []).find(match => match.stage === "Final") || null;
      playoffBracketOutput.innerHTML = '';
      setScheduleFinalsArea(
        !!(finalMatch?.team1 && finalMatch?.team2),
        finalMatch?.team1 && finalMatch?.team2 ? 'The knockout championship match is ready.' : '',
        'Knockout Final'
      );
      renderFinalMatch(finalMatch);
      saveTournament();
      renderGlobalLeaderboard(computeGlobalLeaderboardRows());
      return;
    }

    const groupAssignments = tournament.type === 'Groups' ? tournament.groupAssignments || [] : [];
    const groupStandings = tournament.type === 'Groups' ? computeGroupStandings(groupAssignments) : null;
    const leagueStandings = computeLeagueStandings();

    if (tournament.type === 'Groups' && groupStandings) {
      renderPointsTableAll(groupStandings);
    } else {
      renderPointsTableAll(leagueStandings);
    }

    const knockout = ensureKnockoutStructure();
    const scheduleCompletion = getRegularScheduleCompletion();
    const playoffFormat = tournament.playoffFormat || 'Semifinals';
    const playoffFormatLabel = getPlayoffFormatLabel(playoffFormat);

    if (!scheduleCompletion.ready) {
      resetKnockoutStructure(knockout);
      tournament.finalMatch = null;
      tournament.finalResult = null;
      playoffBracketOutput.innerHTML = '';
      finalMatchOutput.innerHTML = '';
      setScheduleFinalsArea(
        scheduleCompletion.total > 0,
        `${scheduleCompletion.completed} of ${scheduleCompletion.total} scheduled games completed. Enter every score to generate the ${playoffFormatLabel.toLowerCase()}.`,
        playoffFormatLabel
      );
      saveTournament();
      renderGlobalLeaderboard(computeGlobalLeaderboardRows());
      return;
    }

    setScheduleFinalsArea(true, 'Regular schedule complete. Finals fixtures were generated automatically from the standings.', playoffFormatLabel);
    const seeds = computeKnockoutParticipants(groupStandings, leagueStandings);
    const directFinalSeeds = computeDirectFinalParticipants(groupStandings, leagueStandings);

    if (playoffFormat === 'Final') {
      clearMatchParticipants(knockout.semifinal1);
      clearMatchParticipants(knockout.semifinal2);
      clearMatchParticipants(knockout.qualifier1);
      clearMatchParticipants(knockout.eliminator);
      clearMatchParticipants(knockout.qualifier2);
      if (directFinalSeeds.length >= 2) {
        assignMatchParticipants(knockout.final, directFinalSeeds[0], directFinalSeeds[1]);
      } else {
        clearMatchParticipants(knockout.final);
      }
    } else if (playoffFormat === 'Semifinals') {
      if (seeds.length >= 4) {
        assignMatchParticipants(knockout.semifinal1, seeds[0], seeds[3]);
        assignMatchParticipants(knockout.semifinal2, seeds[1], seeds[2]);
        const semi1Outcome = getMatchOutcome(knockout.semifinal1);
        const semi2Outcome = getMatchOutcome(knockout.semifinal2);
        assignMatchParticipants(knockout.final,
          semi1Outcome ? semi1Outcome.winner : '',
          semi2Outcome ? semi2Outcome.winner : ''
        );
      } else {
        clearMatchParticipants(knockout.semifinal1);
        clearMatchParticipants(knockout.semifinal2);
        clearMatchParticipants(knockout.final);
      }
    } else {
      // Qualifier flow: seeds[0]=1, seeds[1]=2, seeds[2]=3, seeds[3]=4
      if (seeds.length >= 4) {
        assignMatchParticipants(knockout.qualifier1, seeds[0], seeds[1]);
        assignMatchParticipants(knockout.eliminator, seeds[2], seeds[3]);

        const qualifier1Outcome = getMatchOutcome(knockout.qualifier1);
        const eliminatorOutcome = getMatchOutcome(knockout.eliminator);

        const qualifier1Loser = qualifier1Outcome ? qualifier1Outcome.loser : '';
        const eliminatorWinner = eliminatorOutcome ? eliminatorOutcome.winner : '';
        assignMatchParticipants(knockout.qualifier2, qualifier1Loser, eliminatorWinner);

        const qualifier2Outcome = getMatchOutcome(knockout.qualifier2);
        const qualifier1Winner = qualifier1Outcome ? qualifier1Outcome.winner : '';
        const qualifier2Winner = qualifier2Outcome ? qualifier2Outcome.winner : '';
        assignMatchParticipants(knockout.final, qualifier1Winner, qualifier2Winner);
      } else {
        clearMatchParticipants(knockout.qualifier1);
        clearMatchParticipants(knockout.eliminator);
        clearMatchParticipants(knockout.qualifier2);
        clearMatchParticipants(knockout.final);
      }
    }

    // Sync the saved final match payload with the knockout final pairing/scores
    if (!knockout.final.team1 && !knockout.final.team2) {
      tournament.finalMatch = null;
      tournament.finalResult = null;
    } else {
      tournament.finalMatch = {
        id: knockout.final.id,
        team1: knockout.final.team1,
        team2: knockout.final.team2,
        score1: knockout.final.score1,
        score2: knockout.final.score2,
        stage: 'Final',
        groupIndex: null,
      };
    }

    // Global leaderboard is recomputed from all saved tournaments
    renderGlobalLeaderboard(computeGlobalLeaderboardRows());

    renderKnockoutBracket(knockout, playoffFormat);

  renderFinalMatch(tournament.finalMatch);

  // Persist recalculated structures so Home tabs and reloads stay in sync
  saveTournament();
  }

function createFinalsScheduleTable(matches, handleScore) {
  const table = document.createElement('table');
  table.className = 'scheduleTable';
  const caption = document.createElement('caption');
  caption.className = 'srOnly';
  caption.textContent = 'Finals score entry';
  table.appendChild(caption);
  const thead = document.createElement('thead');
  const headerRow = document.createElement('tr');
  ['Stage', 'Team 1', 'Score', 'vs', 'Score', 'Team 2'].forEach(label => {
    const th = document.createElement('th');
    th.scope = 'col';
    th.textContent = label;
    headerRow.appendChild(th);
  });
  thead.appendChild(headerRow);
  table.appendChild(thead);
  const tbody = document.createElement('tbody');

  matches.forEach(match => {
    const row = document.createElement('tr');
    const stage = document.createElement('td');
    stage.textContent = match.stage || 'Final';
    row.appendChild(stage);

    const team1 = document.createElement('td');
    appendTeamPhotoDisplay(team1, match.team1, { align: 'end' });
    row.appendChild(team1);

    const addScoreCell = (scoreKey) => {
      const cell = document.createElement('td');
      const input = document.createElement('input');
      input.type = 'number';
      input.className = 'scoreInput';
      input.dataset.matchId = String(match.id);
      input.dataset.scoreSide = scoreKey === 'score1' ? '1' : '2';
      input.inputMode = 'numeric';
      input.setAttribute('aria-label', `${getTeamDisplayName(scoreKey === 'score1' ? match.team1 : match.team2)} score for ${match.stage || 'final'} match ${match.id}`);
      input.style.width = '70px';
      input.min = 0;
      input.value = match[scoreKey] ?? '';
      const update = (phase) => {
        match[scoreKey] = input.value === '' ? null : Number(input.value);
        handleScore?.(match, scoreKey, phase);
      };
      input.addEventListener('input', () => update('input'));
      input.addEventListener('change', () => update('change'));
      cell.appendChild(input);
      row.appendChild(cell);
      return input;
    };

    const score1Input = addScoreCell('score1');
    const separator = document.createElement('td');
    separator.textContent = 'vs';
    row.appendChild(separator);
    const score2Input = addScoreCell('score2');
    score1Input.addEventListener('input', () => {
      if ((score1Input.value || '').replace(/\D/g, '').length >= 2) {
        score2Input.focus();
      }
    });

    const team2 = document.createElement('td');
    appendTeamPhotoDisplay(team2, match.team2);
    row.appendChild(team2);
    tbody.appendChild(row);
  });

  table.appendChild(tbody);
  return table;
}

function renderFinalMatch(finalMatch) {
  finalMatchOutput.innerHTML = '';
  if (!tournament) {
    finalMatchOutput.innerHTML = '<div class="hint">Load or create a tournament to see the championship match.</div>';
    return;
  }

  if (!finalMatch || !finalMatch.team1 || !finalMatch.team2) {
    finalMatchOutput.innerHTML = '<div class="hint">Final matchup will appear once playoff winners are decided.</div>';
    tournament.finalResult = null;
    saveTournament();
    return;
  }

  const finalTable = createFinalsScheduleTable([finalMatch], (match, scoreKey) => {
    if (tournament.finalMatch) tournament.finalMatch[scoreKey] = match[scoreKey];
    if (tournament.knockout?.final) tournament.knockout.final[scoreKey] = match[scoreKey];
    tournament.finalResult = computeFinalResultFromFinalMatch(match);
    saveTournament();
    renderFinalSummary(match);
    renderGlobalLeaderboard(computeGlobalLeaderboardRows());
  });
  finalMatchOutput.appendChild(finalTable);
  renderFinalSummary(finalMatch);
}

function renderFinalSummaryLegacy(finalMatch) {
  const existing = finalMatchOutput.querySelector('.finalSummary');
  if (existing) existing.remove();

  const summary = document.createElement('div');
  summary.className = 'finalSummary';
  summary.style.marginTop = '16px';
  summary.style.display = 'flex';
  summary.style.flexDirection = 'column';
  summary.style.gap = '12px';

  const result = computeFinalResultFromFinalMatch(finalMatch);
  if (!result) {
    summary.style.fontSize = '13px';
    summary.style.color = 'var(--muted)';
    summary.textContent = 'Enter scores above to lock in champion and runner-up.';
    tournament.finalResult = null;
    saveTournament();
  } else {
    tournament.finalResult = result;
    saveTournament();

    const awards = document.createElement('div');
    awards.className = 'finalAwards';

    const createAward = (kind, label, name) => {
      const award = document.createElement('div');
      award.className = `finalAward ${kind}`;
      const icon = document.createElement('span');
      icon.className = `finalAwardIcon ${kind === 'winner' ? 'trophy' : 'plate'}`;
      icon.setAttribute('role', 'img');
      icon.setAttribute('aria-label', kind === 'winner' ? 'Winner trophy' : 'Runner-up plate');
      if (kind === 'winner') icon.textContent = '\u{1F3C6}';
      const copy = document.createElement('div');
      const awardLabel = document.createElement('div');
      awardLabel.className = 'finalAwardLabel';
      awardLabel.textContent = label;
      const awardName = document.createElement('div');
      awardName.className = 'finalAwardName';
      awardName.textContent = name;
      copy.append(awardLabel, awardName);
      award.append(icon, copy);
      return award;
    };

    awards.append(
      createAward('winner', 'Winner · Champion', getTeamDisplayName(result.winner)),
      createAward('runnerUp', 'Runner-up', getTeamDisplayName(result.runnerUp))
    );
    summary.appendChild(awards);
  }

  finalMatchOutput.prepend(summary);
}

function renderFinalSummary(finalMatch) {
  const existing = finalMatchOutput.querySelector('.finalSummary');
  if (existing) existing.remove();

  const summary = document.createElement('div');
  summary.className = 'finalSummary';
  summary.style.marginTop = '16px';
  summary.style.display = 'flex';
  summary.style.flexDirection = 'column';
  summary.style.gap = '12px';

  const result = computeFinalResultFromFinalMatch(finalMatch);
  if (!result) {
    summary.style.fontSize = '13px';
    summary.style.color = 'var(--muted)';
    summary.textContent = 'Enter scores above to lock in champion and runner-up.';
    tournament.finalResult = null;
    saveTournament();
  } else {
    tournament.finalResult = result;
    saveTournament();

    const awards = document.createElement('div');
    awards.className = 'finalAwards';

    const awardPlayersForTeam = (team) => {
      const assigned = (tournament?.teamPlayers?.[team] || [])
        .map(normalizePlayerName)
        .filter(Boolean);
      return assigned.length ? assigned : [getTeamDisplayName(team)];
    };

    const createAwardPlayer = (playerName) => {
      const item = document.createElement('div');
      item.className = 'awardPlayer';
      const photo = document.createElement('div');
      photo.className = 'awardPlayerPhoto';
      const photoUrl = getPlayerPhoto(playerName);
      if (photoUrl) {
        const img = document.createElement('img');
        img.src = photoUrl;
        img.alt = '';
        photo.appendChild(img);
      } else {
        photo.textContent = playerInitials(playerName);
      }
      const name = document.createElement('div');
      name.className = 'awardPlayerName';
      name.textContent = playerName;
      item.append(photo, name);
      return item;
    };

    const createAward = (kind, label, team) => {
      const award = document.createElement('div');
      award.className = `finalAward ${kind}`;
      const icon = document.createElement('span');
      icon.className = `finalAwardIcon ${kind === 'winner' ? 'trophy' : 'plate'}`;
      icon.setAttribute('role', 'img');
      icon.setAttribute('aria-label', kind === 'winner' ? 'Winner trophy' : 'Runner-up plate');
      if (kind === 'winner') icon.textContent = '\u{1F3C6}';
      const copy = document.createElement('div');
      const awardLabel = document.createElement('div');
      awardLabel.className = 'finalAwardLabel';
      awardLabel.textContent = label;
      const awardName = document.createElement('div');
      awardName.className = 'finalAwardName';
      awardName.textContent = getTeamDisplayName(team);
      const players = document.createElement('div');
      players.className = 'awardPlayers';
      awardPlayersForTeam(team).forEach(player => players.appendChild(createAwardPlayer(player)));
      copy.append(awardLabel, awardName, players);
      award.append(icon, copy);
      return award;
    };

    awards.append(
      createAward('winner', 'Winner - Champion', result.winner),
      createAward('runnerUp', 'Runner-up', result.runnerUp)
    );
    summary.appendChild(awards);
  }

  finalMatchOutput.prepend(summary);
}

  // (Old in-section tabs removed; each feature has its own screen.)

  // ----------------------------
  // Validation for enabling schedule
  // ----------------------------
  function updateGenerateScheduleBtn() {
    const disableButton = (btn) => {
      if (!btn) return;
      btn.disabled = true;
      btn.style.pointerEvents = "none";
      btn.style.opacity = "0.6";
    };
    const setButtonState = (btn, enabled) => {
      if (!btn) return;
      btn.disabled = !enabled;
      btn.style.pointerEvents = enabled ? "auto" : "none";
      btn.style.opacity = enabled ? "1" : "0.6";
    };

    if (!tournament) {
      disableButton(generateScheduleBtn);
      disableButton(groupsGenerateScheduleBtn);
      disableButton(autoFillGroupsBtn);
      updateBottomNavState();
      return;
    }

    const typeOk = !!tournamentTypeSelect.value;
    const fixtureOk = tournamentTypeSelect.value === 'Knockout' || !!fixtureTypeSelect.value;
    const builtTeamsCount = (tournament.teams || []).length;
    const requestedTeamsCount = getRequestedTeamsCount();
    const teamsOk = builtTeamsCount >= 2 && builtTeamsCount === requestedTeamsCount;
    const assignmentsOk = teamsOk && validateTeamAssignments();
    const groupsEnabled = tournamentTypeSelect.value === "Groups";
    const knockoutEnabled = tournamentTypeSelect.value === "Knockout";

    let groupsOk = true;
    if (groupsEnabled) {
      const gc = parseInt(groupsCountInput.value, 10);
      const tpg = parseInt(teamsPerGroupInput.value, 10);
      groupsOk = Number.isFinite(gc)
        && gc >= 1
        && Number.isFinite(tpg)
        && tpg >= 2
        && (gc * tpg) <= builtTeamsCount
        && hasCompleteGroupAssignments();
    }

    const knockoutOk = !knockoutEnabled || requestedTeamsCount >= 2;
    const canGenerate = typeOk && fixtureOk && teamsOk && assignmentsOk && groupsOk && knockoutOk;
    const canOpenGroupAssignment = typeOk && fixtureOk && teamsOk;
    setButtonState(generateScheduleBtn, groupsEnabled ? canOpenGroupAssignment : canGenerate);
    setButtonState(groupsGenerateScheduleBtn, canGenerate);
    setButtonState(autoFillGroupsBtn, groupsEnabled && teamsOk);
    const primaryGenerateLabel = groupsEnabled && teamsOk && (!groupsOk || !assignmentsOk) ? 'Assign Group Players' : 'Generate Schedule';
    generateScheduleBtn.textContent = primaryGenerateLabel;
    if (groupsGenerateScheduleBtn) groupsGenerateScheduleBtn.textContent = 'Generate Schedule';
    updateBottomNavState();
  }

  // ----------------------------
  // Create / Load tournament flows
  // ----------------------------
  function createEmptyTournamentRecord(name, scheduledDate = '') {
    return {
      id: uid(),
      name,
      scheduledDate,
      type: '',
      fixtureType: '',
      matchType: '',
      playoffFormat: 'Semifinals',
      teamsCount: 0,
      groupsCount: 0,
      teamsPerGroup: 0,
      knockoutRoundOf: 0,
      players: [...getHomePlayersDraft()],
      playerPhotos: getHomePlayerPhotos(),
      teams: [],
      teamPlayers: {},
      groupAssignments: [],
      matches: [],
      finalMatch: null,
      finalResult: null,
    };
  }

  function createTournament(name) {
    tournament = createEmptyTournamentRecord(name);
    expandedPointsTeam = null;
    tournamentEntryMode = 'create';
    upsertTournamentIndex(tournament);
    saveTournament();
  }

  function loadTournament(id) {
    const t = loadTournamentById(id);
    if (!t) return;
    tournament = {
      groupAssignments: [],
      playerPhotos: {},
      ...t,
    };
    tournament.playerPhotos = normalizePlayerPhotoMap(tournament.playerPhotos || {});
    expandedPointsTeam = null;
    tournamentEntryMode = 'load';
    localStorage.setItem(STORAGE_KEYS.activeTournamentId, id);
  }

  function bindTournamentToUI() {
    if (!tournament) return;
    currentTournamentNameEl.textContent = tournament.name;

    tournamentTypeSelect.value = tournament.type || '';
    fixtureTypeSelect.value = tournament.fixtureType || '';
    matchTypeSelect.value = tournament.matchType || '';
    playoffFormatSelect.value = tournament.playoffFormat || 'Semifinals';
    teamsCountInput.value = tournament.teamsCount || '';
    groupsCountInput.value = tournament.groupsCount || '';
    teamsPerGroupInput.value = tournament.teamsPerGroup || '';
    knockoutRoundOfInput.value = tournament.knockoutRoundOf || '';

    groupsConfig.classList.toggle('hidden', tournamentTypeSelect.value !== 'Groups');
    knockoutConfig.classList.toggle('hidden', tournamentTypeSelect.value !== 'Knockout');

    // Keep screens in sync with loaded tournament
    clearOutputs();
    renderPlayersList();
    renderTeamsPreview();
    renderGroupsAssignment();
    if (Array.isArray(tournament.matches) && tournament.matches.length > 0) {
      renderSchedule(tournament.matches);
      recalcAndRender();
    }
    updateGenerateScheduleBtn();
    savePlayerListBtn.disabled = (tournament.players || []).length === 0;
    updateBuildTeamsBtn();
    updateBottomNavState();
  }

  // ----------------------------
  // Event wiring
  // ----------------------------
  startCreateTournamentBtn?.addEventListener('click', () => {
    tournamentEntryMode = 'create';
    newTournamentNameInput.value = '';
    createTournamentBtn.disabled = true;
    loadTournamentSelect.value = '';
    loadTournamentBtn.disabled = true;
    deleteTournamentBtn.disabled = true;
    updateTournamentHomePanels();
    window.setTimeout(() => newTournamentNameInput.focus(), 0);
  });

  startLoadTournamentBtn?.addEventListener('click', () => {
    tournamentEntryMode = 'load';
    newTournamentNameInput.value = '';
    createTournamentBtn.disabled = true;
    refreshHomeDropdowns();
    updateTournamentHomePanels();
    window.setTimeout(() => loadTournamentSelect.focus(), 0);
  });

  tournamentCreateBackBtn?.addEventListener('click', () => {
    tournamentEntryMode = null;
    newTournamentNameInput.value = '';
    createTournamentBtn.disabled = true;
    updateTournamentHomePanels();
    startCreateTournamentBtn?.focus();
  });

  tournamentLoadBackBtn?.addEventListener('click', () => {
    tournamentEntryMode = null;
    loadTournamentSelect.value = '';
    loadTournamentBtn.disabled = true;
    deleteTournamentBtn.disabled = true;
    updateTournamentHomePanels();
    startLoadTournamentBtn?.focus();
  });

  newTournamentNameInput.addEventListener('input', () => {
    createTournamentBtn.disabled = newTournamentNameInput.value.trim() === '';
  });

  createTournamentBtn.addEventListener('click', () => {
    const name = newTournamentNameInput.value.trim();
    if (!name) return;
    createTournament(name);
    newTournamentNameInput.value = '';
    createTournamentBtn.disabled = true;
    bindTournamentToUI();
    setView('tournament');
  });

  loadTournamentSelect.addEventListener('change', () => {
    loadTournamentBtn.disabled = !loadTournamentSelect.value;
    deleteTournamentBtn.disabled = !loadTournamentSelect.value;
  });

  loadTournamentBtn.addEventListener('click', () => {
    if (!loadTournamentSelect.value) return;
    loadTournament(loadTournamentSelect.value);
    bindTournamentToUI();
    setView('tournament');
  });

  deleteTournamentBtn.addEventListener('click', () => {
    const id = loadTournamentSelect.value;
    if (!id) return;
    const ok = confirm('Delete this tournament? This cannot be undone.');
    if (!ok) return;
    deleteTournamentById(id);
    refreshHomeDropdowns();
    if (currentView === 'history') {
      refreshHistoryDropdown();
      renderSelectedHistoryTournament();
    }
  });

  closeTournamentBtn.addEventListener('click', () => {
    tournament = null;
    expandedPointsTeam = null;
    tournamentEntryMode = null;
    localStorage.removeItem(STORAGE_KEYS.activeTournamentId);
    localStorage.setItem(STORAGE_KEYS.activeView, 'tournament');
    currentTournamentNameEl.textContent = '-';
    if (tournamentSetupPanel) {
      tournamentSetupPanel.classList.add('hidden');
      tournamentSetupPanel.hidden = true;
    }
    tournamentTypeSelect.value = '';
    fixtureTypeSelect.value = '';
    matchTypeSelect.value = '';
    teamsCountInput.value = '';
    groupsCountInput.value = '';
    teamsPerGroupInput.value = '';
    knockoutRoundOfInput.value = '';
    groupsConfig.classList.add('hidden');
    knockoutConfig.classList.add('hidden');
    clearOutputs();
    renderPlayersList();
    renderTeamsPreview();
    renderGroupsAssignment();
    updateGenerateScheduleBtn();
    updateBuildTeamsBtn();
    refreshHomeDropdowns();
    updateBottomNavState();
    setView('tournament');
  });

  resetTournamentBtn.addEventListener('click', () => {
    if (!tournament) return;
    const ok = confirm('Reset tournament data (matches, scores, final match)? Teams and config will remain.');
    if (!ok) return;
    tournament.matches = [];
    tournament.finalMatch = null;
    tournament.finalResult = null;
    saveTournamentAndRefresh();
    clearOutputs();
    updateBottomNavState();
  });

  tournamentTypeSelect.addEventListener('change', () => {
    if (!tournament) return;
    tournament.type = tournamentTypeSelect.value;
    groupsConfig.classList.toggle("hidden", tournament.type !== "Groups");
    knockoutConfig.classList.toggle("hidden", tournament.type !== "Knockout");
    if (tournament.type === "Knockout") {
      tournament.fixtureType = "";
      tournament.playoffFormat = "Final";
      fixtureTypeSelect.value = "";
      playoffFormatSelect.value = "Final";
      const effectiveRoundOf = getEffectiveKnockoutRoundOf();
      tournament.teamsCount = effectiveRoundOf;
      teamsCountInput.value = effectiveRoundOf || "";
    }
    if (tournament.type === "Groups") {
      tournament.groupAssignments = normalizeGroupAssignments(autoFillGroupAssignments());
    }
    saveTournamentAndRefresh();
    renderTeamsPreview();
    renderGroupsAssignment();
    updateGenerateScheduleBtn();
  });

  knockoutRoundOfInput.addEventListener("input", () => {
    if (!tournament) return;
    const roundOf = getKnockoutRoundOf();
    tournament.knockoutRoundOf = roundOf;
    const effectiveRoundOf = getEffectiveKnockoutRoundOf();
    tournament.teamsCount = effectiveRoundOf;
    teamsCountInput.value = effectiveRoundOf || "";
    tournament.teams = [];
    tournament.teamPlayers = {};
    saveTournamentAndRefresh();
    renderTeamsPreview();
    renderGroupsAssignment();
    updateBuildTeamsBtn();
    updateGenerateScheduleBtn();
  });

  groupsCountInput.addEventListener("input", () => {
    if (!tournament) return;
    tournament.groupsCount = parseInt(groupsCountInput.value || "0", 10);
    if (tournament.type === 'Groups') {
      tournament.groupAssignments = normalizeGroupAssignments(tournament.groupAssignments || []);
      renderTeamsPreview();
      renderGroupsAssignment();
    }
    saveTournamentAndRefresh();
    updateGenerateScheduleBtn();
  });

  teamsPerGroupInput.addEventListener('input', () => {
    if (!tournament) return;
    tournament.teamsPerGroup = parseInt(teamsPerGroupInput.value || '0', 10);
    if (tournament.type === 'Groups') {
      tournament.groupAssignments = normalizeGroupAssignments(tournament.groupAssignments || []);
      renderTeamsPreview();
      renderGroupsAssignment();
    }
    saveTournamentAndRefresh();
    updateGenerateScheduleBtn();
  });

  fixtureTypeSelect.addEventListener('change', () => {
    if (!tournament) return;
    tournament.fixtureType = fixtureTypeSelect.value;
    saveTournamentAndRefresh();
    updateGenerateScheduleBtn();
  });

  playoffFormatSelect.addEventListener('change', () => {
    if (!tournament) return;
    tournament.playoffFormat = playoffFormatSelect.value;
    saveTournamentAndRefresh();
    recalcAndRender();
  });

  playerListSelect.addEventListener('change', () => {
    loadPlayerListBtn.disabled = !playerListSelect.value;
  });

  if (playerStatsSelect) {
    playerStatsSelect.addEventListener('change', () => {
      if (playerStatsSelect.value) {
        localStorage.setItem(STORAGE_KEYS.playerStatsPlayer, playerStatsSelect.value);
      } else {
        localStorage.removeItem(STORAGE_KEYS.playerStatsPlayer);
      }
      renderSelectedPlayerStats();
    });
  }

  const updatePlayerStatsPeriodSelection = () => {
    if (!playerStatsMonthSelect?.value) return;
    const allTime = playerStatsMonthSelect.value === 'all';
    if (!allTime && !playerStatsYearSelect?.value) return;
    playerStatsYearSelect.disabled = allTime;
    localStorage.setItem(STORAGE_KEYS.playerStatsPeriod, allTime
      ? 'all'
      : `${playerStatsYearSelect.value}-${playerStatsMonthSelect.value}`);
    renderSelectedPlayerStats();
  };
  playerStatsYearSelect?.addEventListener('change', updatePlayerStatsPeriodSelection);
  playerStatsMonthSelect?.addEventListener('change', updatePlayerStatsPeriodSelection);

  const updateMonthlyLeaderboardSelection = () => {
    if (!leaderboardMonthSelect?.value) return;
    const allTime = leaderboardMonthSelect.value === 'all';
    if (!allTime && !leaderboardYearSelect?.value) return;
    leaderboardYearSelect.disabled = allTime;
    localStorage.setItem(STORAGE_KEYS.leaderboardPeriod, allTime
      ? 'all'
      : `${leaderboardYearSelect.value}-${leaderboardMonthSelect.value}`);
    renderGlobalLeaderboard(computeGlobalLeaderboardRows());
  };
  leaderboardYearSelect?.addEventListener('change', updateMonthlyLeaderboardSelection);
  leaderboardMonthSelect?.addEventListener('change', updateMonthlyLeaderboardSelection);

  if (historyTournamentSelect) {
    historyTournamentSelect.addEventListener('change', () => {
      if (historyTournamentSelect.value) {
        localStorage.setItem(STORAGE_KEYS.historyTournamentId, historyTournamentSelect.value);
      } else {
        localStorage.removeItem(STORAGE_KEYS.historyTournamentId);
      }
      renderSelectedHistoryTournament();
    });
  }

  historyPreviousMonthBtn?.addEventListener('click', () => {
    const cursor = historyCalendarCursor || historyDateFromKey(localStorage.getItem(STORAGE_KEYS.historyDate)) || new Date();
    historyCalendarCursor = new Date(cursor.getFullYear(), cursor.getMonth() - 1, 1);
    renderHistoryCalendar();
  });

  historyNextMonthBtn?.addEventListener('click', () => {
    const cursor = historyCalendarCursor || historyDateFromKey(localStorage.getItem(STORAGE_KEYS.historyDate)) || new Date();
    historyCalendarCursor = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 1);
    renderHistoryCalendar();
  });

  historyScheduledTournamentName?.addEventListener('input', () => {
    const selectedDate = localStorage.getItem(STORAGE_KEYS.historyDate) || '';
    historyScheduleTournamentBtn.disabled = !(selectedDate && historyScheduledTournamentName.value.trim());
    setAuthStatus(historyScheduleStatus, '');
  });

  historyScheduleTournamentBtn?.addEventListener('click', () => {
    const name = historyScheduledTournamentName.value.trim();
    const scheduledDate = localStorage.getItem(STORAGE_KEYS.historyDate) || '';
    if (!name || !historyDateFromKey(scheduledDate)) return;

    const scheduledTournament = createEmptyTournamentRecord(name, scheduledDate);
    upsertTournamentIndex(scheduledTournament);
    localStorage.setItem(STORAGE_KEYS.tournamentPrefix + scheduledTournament.id, JSON.stringify(scheduledTournament));
    localStorage.setItem(STORAGE_KEYS.historyTournamentId, scheduledTournament.id);
    historyScheduledTournamentName.value = '';
    historyScheduleTournamentBtn.disabled = true;
    refreshHomeDropdowns();
    refreshHistoryDropdown();
    historyTournamentSelect.value = scheduledTournament.id;
    renderSelectedHistoryTournament();
    setAuthStatus(historyScheduleStatus, `Scheduled ${name} for ${historyDateFromKey(scheduledDate).toLocaleDateString()}.`, 'success');
  });

  loadPlayerListBtn.addEventListener('click', () => {
    const pl = loadPlayerList(playerListSelect.value);
    if (!pl) return;
    setActivePlayersList(pl.players || []);
    setActivePlayerPhotos(pl.playerPhotos || pl.photos || {});
    renderPlayersList();
    updateSavePlayerListBtnState();
    if (tournament) {
      renderTeamsPreview();
      updateBuildTeamsBtn();
      updateGenerateScheduleBtn();
      saveTournamentAndRefresh();
    }
  });

  savePlayerListBtn.addEventListener('click', () => {
    const players = getActivePlayersList();
    if (!players || players.length === 0) return;
    const defaultName = tournament ? `Players - ${tournament.name}` : 'Draft Players';
    const name = prompt('Save player list name:', defaultName);
    if (!name) return;
    if (!validatePlayersUniquenessForList(players)) {
      alert('Please ensure all players are filled and unique before saving.');
      return;
    }
    upsertPlayerList(name, players);
    refreshHomeDropdowns();
    alert('Player list saved.');
  });

  const setBulkPlayersPanelOpen = (open) => {
    if (!bulkPlayersPanel || !toggleBulkPlayersBtn) return;
    bulkPlayersPanel.hidden = !open;
    toggleBulkPlayersBtn.setAttribute('aria-expanded', String(open));
    toggleBulkPlayersBtn.title = open ? 'Hide many-player entry' : 'Add many players';
    toggleBulkPlayersBtn.setAttribute('aria-label', open ? 'Hide many-player entry' : 'Add many players');
    if (open) {
      window.requestAnimationFrame(() => bulkPlayersInput?.focus());
    }
  };

  toggleBulkPlayersBtn?.addEventListener('click', () => {
    setBulkPlayersPanelOpen(!!bulkPlayersPanel?.hidden);
  });

  newPlayerNameInput.addEventListener('input', () => {
    addPlayerBtn.disabled = !newPlayerNameInput.value.trim();
  });

  addPlayerBtn.addEventListener('click', () => {
    const name = normalizePlayerName(newPlayerNameInput.value);
    if (!name) return;
    const players = getActivePlayersList();
    if (players.some(p => lower(p) === lower(name))) {
      alert('Player already exists.');
      return;
    }
    players.push(name);
    setActivePlayersList(players);
    newPlayerNameInput.value = '';
    addPlayerBtn.disabled = true;
    updateSavePlayerListBtnState();
    renderPlayersList();
    if (tournament) {
      renderTeamsPreview();
      updateBuildTeamsBtn();
      updateGenerateScheduleBtn();
      saveTournamentAndRefresh();
    }
  });

  bulkPlayersInput.addEventListener('input', () => {
    bulkAddPlayersBtn.disabled = !bulkPlayersInput.value.trim();
  });

  bulkAddPlayersBtn.addEventListener('click', () => {
    const names = splitPlayerNames(bulkPlayersInput.value);
    if (!names.length) return;

    const players = getActivePlayersList();
    const existing = new Set(players.map(p => lower(normalizePlayerName(p))));
    const toAdd = [];
    names.forEach(n => {
      const key = lower(n);
      if (!existing.has(key)) {
        existing.add(key);
        toAdd.push(n);
      }
    });

    if (toAdd.length === 0) {
      alert('No new players to add (all were duplicates).');
      return;
    }

    players.push(...toAdd);
    setActivePlayersList(players);
    bulkPlayersInput.value = '';
    bulkAddPlayersBtn.disabled = true;
    setBulkPlayersPanelOpen(false);
    updateSavePlayerListBtnState();
    renderPlayersList();
    if (tournament) {
      renderTeamsPreview();
      updateBuildTeamsBtn();
      updateGenerateScheduleBtn();
      saveTournamentAndRefresh();
    }
  });

  matchTypeSelect.addEventListener('change', () => {
    if (!buildTeamsBtn || !tournament) return;
    tournament.matchType = matchTypeSelect.value;
    saveTournamentAndRefresh();
    renderTeamsPreview();
    updateBuildTeamsBtn();
    updateGenerateScheduleBtn();
  });

  teamsCountInput.addEventListener('input', () => {
    if (!buildTeamsBtn || !tournament) return;
    tournament.teamsCount = parseInt(teamsCountInput.value || '0', 10);
    saveTournamentAndRefresh();
    renderTeamsPreview();
    updateBuildTeamsBtn();
    updateGenerateScheduleBtn();
  });

  if (buildTeamsBtn) {
  buildTeamsBtn.addEventListener('click', () => {
    if (!tournament) return;
    const teamsCount = getRequestedTeamsCount();
    const req = requiredPlayersPerTeam();
    if (!Number.isFinite(teamsCount) || teamsCount < 2) {
      alert(tournament.type === "Knockout" ? "Please enter a valid knockout round size (min 2)." : "Please enter a valid number of teams (min 2).");
      return;
    }
    if (req === 0) {
      alert('Please select Singles or Doubles.');
      return;
    }

    const teams = [];
    const teamPlayers = {};
    const existingTeamPlayers = tournament.teamPlayers || {};
    const empty = new Array(req).fill("");
    const requestedRoundOf = tournament.type === "Knockout" ? getKnockoutRoundOf() : teamsCount;
    for (let i = 1; i <= teamsCount; i++) {
      const isBot = tournament.type === "Knockout" && requestedRoundOf % 2 === 1 && i === teamsCount;
      const teamName = isBot ? "BOT" : "Team " + i;
      const existing = Array.isArray(existingTeamPlayers[teamName]) ? existingTeamPlayers[teamName] : [];
      teams.push(teamName);
      teamPlayers[teamName] = isBot ? [] : empty.map((_, index) => existing[index] || "");
    }
    tournament.teamsCount = teamsCount;

    if ((!tournament.players || tournament.players.length === 0)) {
      const fallbackPlayers = getPlayersForTeamAssignment();
      if (fallbackPlayers.length > 0) {
        tournament.players = [...fallbackPlayers];
      }
    }

    tournament.teams = teams;
    tournament.teamPlayers = teamPlayers;
    if (tournament.type === 'Groups') {
      tournament.groupAssignments = normalizeGroupAssignments(autoFillGroupAssignments());
    }

    saveTournamentAndRefresh();
    renderTeamsPreview();
    renderGroupsAssignment();
    updateGenerateScheduleBtn();
  });
  }

  function generateScheduleFromCurrentTournament() {
    if (!tournament) return;
    if (!tournament.teams || tournament.teams.length < 2) {
      alert('Please build teams first.');
      return;
    }

    if (!validateTeamAssignments()) {
      alert('Please complete all team player selections first.');
      showTournamentTeamsSetup();
      return;
    }

    matchIdCounter = 1;
    const existingMatches = Array.isArray(tournament.matches) ? tournament.matches : [];
    let nextMatches = [];
    let nextGroupAssignments = [];

    if (tournament.type === "League") {
      nextMatches = buildLeagueMatches(tournament.teams, tournament.fixtureType);
    } else if (tournament.type === "Knockout") {
      nextMatches = buildKnockoutMatches(tournament.teams);
    } else if (tournament.type === "Groups") {
      if (!hasCompleteGroupAssignments()) {
        renderGroupsAssignment();
        alert('Please review and complete group player selections first.');
        showTournamentTeamsSetup();
        return;
      }
      const configuredGroups = getConfiguredGroups();
      nextGroupAssignments = configuredGroups;
      nextMatches = buildGroupMatches(nextGroupAssignments, tournament.fixtureType);
    } else {
      alert('Select tournament type.');
      return;
    }

    const unchangedSchedule = sameMatchSchedule(existingMatches, nextMatches);
    tournament.groupAssignments = nextGroupAssignments;

    if (!unchangedSchedule) {
      tournament.matches = carryExistingScores(existingMatches, nextMatches);
      tournament.finalMatch = null;
      tournament.finalResult = null;
    } else {
      tournament.matches = existingMatches;
    }

    tournament.scheduleSignature = scheduleSignatureFor(tournament.matches);

    saveTournamentAndRefresh();
    renderSchedule(tournament.matches);
    recalcAndRender();
    updateBottomNavState();
    setView('schedule');
  }

  generateScheduleBtn.addEventListener('click', generateScheduleFromCurrentTournament);
  if (groupsGenerateScheduleBtn) {
    groupsGenerateScheduleBtn.addEventListener('click', generateScheduleFromCurrentTournament);
  }
  if (autoFillGroupsBtn) {
    autoFillGroupsBtn.addEventListener('click', () => {
      if (!tournament) return;
      tournament.groupAssignments = normalizeGroupAssignments(autoFillGroupAssignments());
      saveTournamentAndRefresh();
      renderGroupsAssignment();
      updateGenerateScheduleBtn();
    });
  }

  function renderKnockoutBracket(knockout, playoffFormat) {
    playoffBracketOutput.innerHTML = '';
    if (!knockout) {
      playoffBracketOutput.innerHTML = '<div class="hint">No playoff bracket available.</div>';
      return;
    }

    const matches = [];
    const addAvailableMatch = (match) => {
      if (match?.team1 && match?.team2) matches.push(match);
    };

    if (playoffFormat === 'Semifinals') {
      addAvailableMatch(knockout.semifinal1);
      addAvailableMatch(knockout.semifinal2);
    } else if (playoffFormat === 'Final') {
      const hint = document.createElement('div');
      hint.className = 'hint';
      hint.textContent = 'Direct final format selected: the top two teams from the points table play the championship match.';
      playoffBracketOutput.appendChild(hint);
    } else {
      addAvailableMatch(knockout.qualifier1);
      addAvailableMatch(knockout.eliminator);
      addAvailableMatch(knockout.qualifier2);
    }

    if (matches.length) {
      playoffBracketOutput.appendChild(createFinalsScheduleTable(matches, (match, scoreKey, phase) => {
        if (phase === 'input') {
          saveTournament();
          return;
        }
        recalcAndRender();
      }));
    }
  }



  function setAuthStatus(el, message, kind) {
    if (!el) return;
    el.textContent = message || '';
    el.classList.remove('error', 'success');
    if (kind) el.classList.add(kind);
  }

  function currentAuthSession() {
    return window.btAuth?.getSession?.() || null;
  }

  function showAuthenticatedApp(session) {
    document.body.classList.remove('authLocked');
    window.btAuth.startSessionActivityWatch?.();
    const authSection = document.getElementById('authSection');
    const sessionBar = document.getElementById('sessionBar');
    const signedInUser = document.getElementById('signedInUser');
    if (authSection) authSection.classList.add('hidden');
    if (sessionBar && document.documentElement.dataset.storageMode !== 'local') sessionBar.classList.remove('hidden');
    if (signedInUser) signedInUser.textContent = `${session.displayName || session.username}${session.isAdmin ? ' (admin)' : ''}`;
    if (session.isAdmin) renderUsersAdmin();
    if (typeof updateBottomNavState === 'function') updateBottomNavState();
  }

  function showLoginOrReset() {
    const session = currentAuthSession();
    const authSection = document.getElementById('authSection');
    const loginPanel = document.getElementById('loginPanel');
    const resetPanel = document.getElementById('resetPasswordPanel');
    if (authSection) authSection.classList.remove('hidden');
    if (session?.mustResetPassword) {
      window.btAuth.startSessionActivityWatch?.();
      if (loginPanel) loginPanel.classList.add('hidden');
      if (resetPanel) resetPanel.classList.remove('hidden');
    } else if (session?.token) {
      showAuthenticatedApp(session);
    } else {
      if (loginPanel) loginPanel.classList.remove('hidden');
      if (resetPanel) resetPanel.classList.add('hidden');
      document.body.classList.add('authLocked');
    }
  }

  async function renderUsersAdmin() {
    const list = document.getElementById('usersList');
    const session = currentAuthSession();
    if (!list || !session?.isAdmin) return;
    list.innerHTML = '<div class="hint">Loading users...</div>';
    const res = await window.btAuth.postRpc('list_users', { auth_token: session.token });
    if (!res.ok) {
      list.innerHTML = '<div class="hint">Unable to load users.</div>';
      return;
    }
    const users = await res.json();
    const table = document.createElement('table');
    const thead = document.createElement('thead');
    const header = document.createElement('tr');
    ['Username', 'Name', 'Admin', 'Reset Required', 'Active', 'Actions'].forEach(h => {
      const th = document.createElement('th');
      th.scope = 'col';
      th.textContent = h;
      header.appendChild(th);
    });
    thead.appendChild(header);
    table.appendChild(thead);
    const tbody = document.createElement('tbody');
    users.forEach(user => {
      const tr = document.createElement('tr');
      [user.username, user.display_name, user.is_admin ? 'Yes' : 'No', user.must_reset_password ? 'Yes' : 'No', user.is_active ? 'Yes' : 'No'].forEach(value => {
        const td = document.createElement('td');
        td.textContent = value;
        tr.appendChild(td);
      });
      const actions = document.createElement('td');
      const reset = document.createElement('button');
      reset.type = 'button';
      reset.className = 'smallBtn secondary';
      reset.textContent = 'Reset';
      reset.addEventListener('click', async () => {
        const temp = prompt(`Temporary password for ${user.username}:`);
        if (!temp) return;
        const r = await window.btAuth.postRpc('reset_user_password', { auth_token: session.token, username: user.username, temporary_password: temp });
        if (!r.ok) alert(await r.text());
        await renderUsersAdmin();
      });
      const toggle = document.createElement('button');
      toggle.type = 'button';
      toggle.className = `smallBtn ${user.is_active ? 'danger' : 'secondary'}`;
      toggle.textContent = user.is_active ? 'Disable' : 'Enable';
      toggle.addEventListener('click', async () => {
        const r = await window.btAuth.postRpc('set_user_active', { auth_token: session.token, username: user.username, is_active: !user.is_active });
        if (!r.ok) alert(await r.text());
        await renderUsersAdmin();
      });
      const deleteBtn = document.createElement('button');
      deleteBtn.type = 'button';
      deleteBtn.className = 'smallBtn danger';
      deleteBtn.textContent = 'Delete';
      deleteBtn.disabled = user.username === session.username;
      deleteBtn.title = deleteBtn.disabled ? 'You cannot delete your own account.' : `Delete ${user.username}`;
      deleteBtn.addEventListener('click', async () => {
        const ok = confirm(`Delete user ${user.username}? This will remove their sessions and cannot be undone.`);
        if (!ok) return;
        const r = await window.btAuth.postRpc('delete_user', { auth_token: session.token, username: user.username });
        if (!r.ok) alert(await r.text());
        await renderUsersAdmin();
      });
      actions.appendChild(reset);
      actions.appendChild(document.createTextNode(' '));
      actions.appendChild(toggle);
      actions.appendChild(document.createTextNode(' '));
      actions.appendChild(deleteBtn);
      tr.appendChild(actions);
      tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    list.innerHTML = '';
    list.appendChild(table);
  }

  function bindAuthUi() {
    const session = currentAuthSession();
    if (session?.token && !session.mustResetPassword) showAuthenticatedApp(session);
    else showLoginOrReset();
    const notice = window.btAuth.consumeSessionNotice?.();
    if (notice) setAuthStatus(document.getElementById('loginStatus'), notice, 'error');

    const loginBtn = document.getElementById('loginBtn');
    const resetBtn = document.getElementById('resetMyPasswordBtn');
    const createUserBtn = document.getElementById('createUserBtn');
    const logoutBtns = [document.getElementById('logoutBtn'), document.getElementById('sessionLogoutBtn')].filter(Boolean);

    loginBtn?.addEventListener('click', async () => {
      const username = document.getElementById('loginUsername')?.value.trim();
      const password = document.getElementById('loginPassword')?.value || '';
      const status = document.getElementById('loginStatus');
      setAuthStatus(status, 'Signing in...');
      try {
        const s = await window.btAuth.login(username, password);
        if (s.mustResetPassword) {
          setAuthStatus(status, 'Password reset required.', 'success');
          showLoginOrReset();
          return;
        }
        location.reload();
      } catch (error) {
        const message = String(error?.message || error || '');
        const sessionLimitMessage = 'Login session limit exceeded. Please logout from another device before logging in again.';
        let loginError = 'Invalid username or password.';
        if (message.includes('Login session limit exceeded')) {
          loginError = sessionLimitMessage;
        } else if (/405 not allowed|static host/i.test(message)) {
          loginError = 'The API endpoint did not accept the login request. Confirm the site is hosted on PHP 8.3.19 and api.php is uploaded beside index.php.';
        } else if (/storage|permission|writable|data store/i.test(message)) {
          loginError = 'The server cannot save application data. Confirm the MySQL database credentials are correct and the tables can be created.';
        } else if (/failed to fetch|networkerror|network error|load failed/i.test(message)) {
          const apiUrl = window.btApiUrl || 'api.php';
          loginError = `Cannot reach API at ${apiUrl}. Open ${apiUrl}?action=ping in the browser; it should show JSON.`;
        } else if (/unexpected token|invalid json|invalid api response|no session token|<!doctype|<html|<\?php/i.test(message)) {
          loginError = `${message} Confirm this site is running through PHP 8.3.19 and api.php is uploaded beside index.php.`;
        } else if (message && !message.includes('Invalid username or password')) {
          loginError = `Server login error: ${message.slice(0, 240)}`;
        }
        setAuthStatus(
          status,
          loginError,
          'error'
        );
      }
    });

    resetBtn?.addEventListener('click', async () => {
      const session = currentAuthSession();
      const currentPassword = document.getElementById('currentPasswordInput')?.value || '';
      const newPassword = document.getElementById('newPasswordInput')?.value || '';
      const confirmPassword = document.getElementById('confirmPasswordInput')?.value || '';
      const status = document.getElementById('resetPasswordStatus');
      if (newPassword !== confirmPassword) {
        setAuthStatus(status, 'Passwords do not match.', 'error');
        return;
      }
      setAuthStatus(status, 'Resetting password...');
      const res = await window.btAuth.postRpc('change_my_password', { auth_token: session?.token, current_password: currentPassword, new_password: newPassword });
      if (!res.ok) {
        setAuthStatus(status, 'Password reset failed. Check the current password and minimum length.', 'error');
        return;
      }
      session.mustResetPassword = false;
      window.btAuth.setSession(session);
      window.btAuth.hydrateFromDatabase(session.token);
      location.reload();
    });

    createUserBtn?.addEventListener('click', async () => {
      const session = currentAuthSession();
      const status = document.getElementById('createUserStatus');
      const username = document.getElementById('newUserUsername')?.value.trim();
      const displayName = document.getElementById('newUserDisplayName')?.value.trim();
      const temporaryPassword = document.getElementById('newUserPassword')?.value || '';
      const isAdmin = document.getElementById('newUserIsAdmin')?.value === 'true';
      setAuthStatus(status, 'Creating user...');
      const res = await window.btAuth.postRpc('create_user', { auth_token: session?.token, username, display_name: displayName, temporary_password: temporaryPassword, is_admin: isAdmin });
      if (!res.ok) {
        setAuthStatus(status, 'Could not create user. Check username/password and duplicates.', 'error');
        return;
      }
      ['newUserUsername', 'newUserDisplayName', 'newUserPassword'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
      setAuthStatus(status, 'User created. They must reset this password on first login.', 'success');
      await renderUsersAdmin();
    });

    logoutBtns.forEach(btn => btn.addEventListener('click', () => window.btAuth.logout()));
  }

  if (databaseBackupPanel && !isLocalFileStorageMode()) {
    databaseBackupPanel.classList.add('hidden');
    databaseBackupPanel.hidden = true;
  }

  exportDatabaseBtn?.addEventListener('click', exportLocalDatabase);

  importDatabaseFile?.addEventListener('change', () => {
    importDatabaseBtn.disabled = !importDatabaseFile.files?.length;
    setAuthStatus(databaseBackupStatus, '');
  });

  importDatabaseBtn?.addEventListener('click', async () => {
    const file = importDatabaseFile.files?.[0];
    if (!file) return;
    importDatabaseBtn.disabled = true;
    setAuthStatus(databaseBackupStatus, 'Validating backup...');
    try {
      const imported = await importLocalDatabase(file, importDatabaseMode?.value || 'merge');
      if (!imported) {
        importDatabaseBtn.disabled = false;
        setAuthStatus(databaseBackupStatus, 'Import cancelled.');
      }
    } catch (error) {
      setAuthStatus(databaseBackupStatus, error?.message || 'Could not import this backup file.', 'error');
      importDatabaseBtn.disabled = false;
    }
  });

  [shuttlePurchaseDate, shuttleBoxesBought].filter(Boolean).forEach(el => {
    el.addEventListener('input', updateShuttleFormState);
    el.addEventListener('change', updateShuttleFormState);
  });
  [shuttleTakenDate, shuttleTakenTime, shuttleTakenBy, shuttleTakenQuantity].filter(Boolean).forEach(el => {
    el.addEventListener('input', updateShuttleFormState);
    el.addEventListener('change', updateShuttleFormState);
  });

  addShuttleStockBtn?.addEventListener('click', () => {
    const boxes = Number(shuttleBoxesBought.value);
    if (!Number.isInteger(boxes) || boxes < 1 || !shuttlePurchaseDate.value) return;
    const data = getShuttleData();
    data.purchases.push({
      id: uid(),
      date: shuttlePurchaseDate.value,
      boxes,
      note: shuttlePurchaseNote.value.trim(),
      createdAt: Date.now(),
    });
    saveShuttleData(data);
    shuttleBoxesBought.value = '';
    shuttlePurchaseNote.value = '';
    renderShuttleManagement();
  });

  recordShuttleTakenBtn?.addEventListener('click', () => {
    const quantity = Number(shuttleTakenQuantity.value);
    const person = shuttleTakenBy.value.trim();
    const data = getShuttleData();
    const totals = getShuttleTotals(data);
    setAuthStatus(shuttleFormStatus, '');
    if (!Number.isInteger(quantity) || quantity < 1 || !person || !shuttleTakenDate.value || !shuttleTakenTime.value) return;
    if (quantity > totals.available) {
      setAuthStatus(shuttleFormStatus, `Only ${totals.available} shuttle${totals.available === 1 ? '' : 's'} available in stock.`, 'error');
      return;
    }
    data.transactions.push({
      id: uid(),
      date: shuttleTakenDate.value,
      time: shuttleTakenTime.value,
      person,
      quantity,
      type: shuttleTakenType.value === 'borrowed' ? 'borrowed' : 'used',
      returned: false,
      returnedDate: '',
      createdAt: Date.now(),
    });
    saveShuttleData(data);
    const action = shuttleTakenType.value === 'borrowed' ? 'Borrowed shuttle recorded.' : 'Shuttle usage recorded.';
    shuttleTakenBy.value = '';
    shuttleTakenQuantity.value = '';
    renderShuttleManagement();
    setAuthStatus(shuttleFormStatus, action, 'success');
  });

  // ----------------------------
  // Init
  // ----------------------------
  const shuttleToday = todayForDateInput();
  if (shuttlePurchaseDate && !shuttlePurchaseDate.value) shuttlePurchaseDate.value = shuttleToday;
  if (shuttleTakenDate && !shuttleTakenDate.value) shuttleTakenDate.value = shuttleToday;
  if (shuttleTakenTime && !shuttleTakenTime.value) shuttleTakenTime.value = currentTimeForInput();
  renderShuttleManagement();
  refreshHomeDropdowns();
  renderPlayersList();
  if (bottomNav) {
    bottomNavButtons().forEach(btn => {
      btn.addEventListener('click', () => setView(btn.dataset.view));
    });
    bottomNav.addEventListener('keydown', (event) => {
      const keys = ['ArrowRight', 'ArrowDown', 'ArrowLeft', 'ArrowUp', 'Home', 'End'];
      if (!keys.includes(event.key)) return;
      const tabs = [...bottomNavButtons()].filter(btn => !btn.disabled && !btn.hidden);
      if (!tabs.length) return;
      const currentIndex = Math.max(0, tabs.indexOf(document.activeElement));
      let nextIndex = currentIndex;
      if (event.key === 'Home') nextIndex = 0;
      else if (event.key === 'End') nextIndex = tabs.length - 1;
      else if (event.key === 'ArrowRight' || event.key === 'ArrowDown') nextIndex = (currentIndex + 1) % tabs.length;
      else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') nextIndex = (currentIndex - 1 + tabs.length) % tabs.length;
      event.preventDefault();
      tabs[nextIndex].focus();
      setView(tabs[nextIndex].dataset.view);
    });
  }
  const activeTournamentId = localStorage.getItem(STORAGE_KEYS.activeTournamentId);
  if (activeTournamentId && loadTournamentById(activeTournamentId)) {
    loadTournament(activeTournamentId);
    bindTournamentToUI();
    if (Array.isArray(tournament?.matches) && tournament.matches.length > 0) {
      recalcAndRender();
    }
  }
  updateBottomNavState();
  const savedView = localStorage.getItem(STORAGE_KEYS.activeView) || 'tournament';
  setView(savedView);

  // Dev-friendly: surface errors clearly while still being a single-file HTML app
  window.addEventListener('error', (e) => {
    console.error('Unhandled error:', e.error || e.message);
  });
  window.addEventListener('unhandledrejection', (e) => {
    console.error('Unhandled promise rejection:', e.reason);
  });
  bindAuthUi();
</script>

</body>
</html>
