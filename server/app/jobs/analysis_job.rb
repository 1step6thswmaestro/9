class AnalysisJob < ActiveJob::Base

  def perform
    user_id_list = UserAction.uniq.pluck(:id)
    for i in user_id_list
      p i
    end
    #AnalysisJob.delay_for(1.day).perform_later
  end
end
